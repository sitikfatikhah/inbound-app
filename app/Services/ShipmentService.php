<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\ShipmentServiceInterface;
use App\Models\PurchaseOrderItems;
use App\Models\Shipments;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShipmentService implements ShipmentServiceInterface
{
    private const SYSTEM_MILESTONE_PREFIX = '[System] ';

    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): Shipments
    {
        return DB::transaction(function () use ($data): Shipments {
            $containers = $data['containers'] ?? [];
            $milestones = $data['milestones'] ?? [];
            $shipmentData = collect($data)
                ->except(['location', 'country', 'containers', 'milestones', 'purchase_order_ids'])
                ->all();
            $shipmentData['original_eta'] = $shipmentData['eta'] ?? null;
            $shipmentData['original_etd'] = $shipmentData['etd'] ?? null;
            $shipment = Shipments::create($shipmentData);

            foreach (config('documents.required_by_type.import', []) as $documentType) {
                $shipment->shipmentDocuments()->create([
                    'document_type' => $documentType,
                    'document_name' => config("documents.types.$documentType", $documentType),
                    'verification_status' => 'missing',
                ]);
            }

            $shipment->purchaseOrders()->sync($this->storeAllocations($shipment, $containers));
            $this->syncMilestones($shipment, $milestones);
            $this->syncPurchaseOrderMilestones($shipment);
            $this->syncScheduleMilestones($shipment);
            $this->recordCurrentStatuses($shipment, $data);

            $this->activity->log(
                module: 'Shipment',
                action: 'CREATE',
                model: $shipment,
                new: $shipment->toArray(),
                description: 'Shipment berhasil dibuat',
            );

            return $shipment->load(['statusHistories', 'milestones', 'purchaseOrders', 'shipmentContainers']);
        });
    }

    public function update(Shipments $shipment, array $data): Shipments
    {
        return DB::transaction(function () use ($shipment, $data): Shipments {
            $old = $shipment->getOriginal();
            $oldDocumentStatus = $shipment->status_document;
            $oldTrackingStatus = $shipment->tracking_status;

            $containers = $data['containers'] ?? null;
            $milestones = $data['milestones'] ?? null;
            $shipment->update(collect($data)
                ->except(['location', 'country', 'containers', 'milestones', 'purchase_order_ids'])
                ->all());

            if ($containers !== null) {
                $this->restoreAllocations($shipment);
                $shipment->purchaseOrders()->sync($this->storeAllocations($shipment, $containers));
                $this->syncPurchaseOrderMilestones($shipment);
            }

            $this->syncScheduleMilestones($shipment);

            if ($milestones !== null) {
                $this->syncMilestones($shipment, $milestones);
            }

            if ($oldDocumentStatus !== $shipment->status_document) {
                $this->recordStatusChange($shipment, 'Document status', $shipment->status_document, $data);
            }

            if ($oldTrackingStatus !== $shipment->tracking_status) {
                $this->recordStatusChange($shipment, 'Tracking status', $shipment->tracking_status, $data);
            }

            $this->activity->log(
                module: 'Shipment',
                action: 'UPDATE',
                model: $shipment,
                old: $old,
                new: $shipment->fresh()->toArray(),
                description: 'Shipment diperbarui'
            );

            return $shipment->fresh(['statusHistories', 'milestones', 'purchaseOrders', 'shipmentContainers']);
        });
    }

    public function delete(Shipments $shipment): bool
    {
        return DB::transaction(function () use ($shipment): bool {
            $old = $shipment->toArray();
            $this->restoreAllocations($shipment);
            $shipment->purchaseOrders()->detach();

            $this->activity->log(
                module: 'Shipment',
                action: 'DELETE',
                model: $shipment,
                old: $old,
                description: 'Shipment dihapus'
            );

            return $shipment->delete();
        });
    }

    public function generateShipmentNumber(): string
    {
        return DB::transaction(function () {
            $today = now()->toDateString();

            // Pastikan sequence untuk tanggal hari ini tersedia.
            DB::table('shipment_number_sequences')
                ->insertOrIgnore([
                    'sequence_date' => $today,
                    'last_number' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            // Lock baris sequence agar tidak digunakan
            // secara bersamaan oleh transaksi lain.
            $sequence = DB::table('shipment_number_sequences')
                ->where('sequence_date', $today)
                ->lockForUpdate()
                ->first();

            $nextNumber = $sequence->last_number + 1;

            DB::table('shipment_number_sequences')
                ->where('sequence_date', $today)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return sprintf(
                'SHP-%s-%04d',
                now()->format('Ymd'),
                $nextNumber
            );
        });
    }

    public function getPurchaseOrderItems(): array
    {
        return DB::table('purchase_order_items')
            ->join(
                'purchase_orders',
                'purchase_orders.id',
                '=',
                'purchase_order_items.purchase_order_id'
            )
            ->select(
                'purchase_order_items.id',
                'purchase_order_items.purchase_order_id',
                'purchase_order_items.product_code',
                'purchase_order_items.product_name',
                'purchase_order_items.quantity',
                'purchase_order_items.backorder_qty',
                'purchase_order_items.remaining_qty',
                'purchase_order_items.unit',
                'purchase_order_items.hs_code',
                'purchase_order_items.facility',
                'purchase_order_items.import_duty',
                'purchase_order_items.total_price',
                'purchase_orders.po_number'
            )
            ->orderBy('purchase_order_items.purchase_order_id')
            ->orderBy('purchase_order_items.id')
            ->get()
            ->map(static fn (object $item): array => (array) $item)
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $containers
     * @return array<int, int>
     */
    private function storeAllocations(Shipments $shipment, array $containers): array
    {
        $entries = [];
        $requestedQuantities = [];
        $firstEntryByItem = [];

        foreach ($containers as $containerIndex => $container) {
            foreach ($container['items'] as $itemIndex => $allocation) {
                $itemId = (int) $allocation['purchase_order_item_id'];
                $quantity = (float) $allocation['quantity'];
                $entries[] = [
                    'container' => $container,
                    'item' => $allocation,
                    'item_id' => $itemId,
                ];
                $requestedQuantities[$itemId] = ($requestedQuantities[$itemId] ?? 0) + $quantity;
                $firstEntryByItem[$itemId] ??= [$containerIndex, $itemIndex];
            }
        }

        if ($entries === []) {
            throw ValidationException::withMessages([
                'containers' => 'At least one PO item allocation is required.',
            ]);
        }

        $items = PurchaseOrderItems::query()
            ->whereKey(array_keys($requestedQuantities))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($entries as $entry) {
            $item = $items->get($entry['item_id']);
            [$containerIndex, $itemIndex] = $firstEntryByItem[$entry['item_id']];

            if (! $item || (int) $item->purchase_order_id !== (int) $entry['item']['purchase_order_id']) {
                throw ValidationException::withMessages([
                    "containers.$containerIndex.items.$itemIndex.purchase_order_item_id" => 'The selected PO item does not belong to the selected purchase order.',
                ]);
            }
        }

        foreach ($requestedQuantities as $itemId => $quantity) {
            $item = $items->get($itemId);
            $availableQuantity = max(0, (float) ($item->remaining_qty ?? $item->quantity ?? 0));

            if ($quantity > $availableQuantity) {
                [$containerIndex, $itemIndex] = $firstEntryByItem[$itemId];

                throw ValidationException::withMessages([
                    "containers.$containerIndex.items.$itemIndex.quantity" => 'The allocated quantity exceeds the remaining PO quantity.',
                ]);
            }

            $item->update([
                'remaining_qty' => $availableQuantity - $quantity,
                'total_allocated' => (float) ($item->total_allocated ?? 0) + $quantity,
            ]);
        }

        $purchaseOrderIds = [];

        foreach ($entries as $entry) {
            $item = $items->get($entry['item_id']);
            $allocation = $entry['item'];
            $container = $entry['container'];

            $shipment->shipmentContainers()->create([
                'purchase_order_item_id' => $item->id,
                'product_code' => $item->product_code ?? (string) $item->product_id,
                'quantity' => $allocation['quantity'],
                'container_number' => $container['container_number'] ?? null,
                'container_size' => $container['container_size'] ?? null,
                'seal_number' => $container['container_seal_number'] ?? null,
            ]);

            $purchaseOrderIds[] = (int) $item->purchase_order_id;
        }

        return array_values(array_unique($purchaseOrderIds));
    }

    private function restoreAllocations(Shipments $shipment): void
    {
        $allocations = $shipment->shipmentContainers()->lockForUpdate()->get();

        if ($allocations->isEmpty()) {
            return;
        }

        $quantityByItem = $allocations->groupBy('purchase_order_item_id')
            ->map(fn ($items): float => (float) $items->sum('quantity'));
        $purchaseOrderItems = PurchaseOrderItems::query()
            ->whereKey($quantityByItem->keys())
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($quantityByItem as $itemId => $quantity) {
            $item = $purchaseOrderItems->get($itemId);

            if (! $item) {
                continue;
            }

            $item->update([
                'remaining_qty' => min(
                    (float) ($item->quantity ?? 0),
                    (float) ($item->remaining_qty ?? 0) + $quantity
                ),
                'total_allocated' => max(0, (float) ($item->total_allocated ?? 0) - $quantity),
            ]);
        }

        $shipment->shipmentContainers()->delete();
    }

    /** @param array<int, array<string, mixed>> $milestones */
    private function syncMilestones(Shipments $shipment, array $milestones): void
    {
        $shipment->milestones()
            ->where('milestone_name', 'not like', self::SYSTEM_MILESTONE_PREFIX.'%')
            ->delete();

        foreach ($milestones as $milestone) {
            $shipment->milestones()->create($milestone);
        }
    }

    private function syncPurchaseOrderMilestones(Shipments $shipment): void
    {
        $shipment->milestones()
            ->where('milestone_name', 'like', self::SYSTEM_MILESTONE_PREFIX.'Purchase Order:%')
            ->delete();

        $shipment->load('purchaseOrders:id,po_number,po_date');

        foreach ($shipment->purchaseOrders as $purchaseOrder) {
            $shipment->milestones()->create([
                'milestone_name' => self::SYSTEM_MILESTONE_PREFIX.'Purchase Order: '.$purchaseOrder->po_number,
                'status' => 'Completed',
                'actual_date' => $purchaseOrder->po_date?->toDateString(),
            ]);
        }
    }

    private function syncScheduleMilestones(Shipments $shipment): void
    {
        $shipment->milestones()
            ->whereIn('milestone_name', [
                self::SYSTEM_MILESTONE_PREFIX.'Estimated Departure (ETD)',
                self::SYSTEM_MILESTONE_PREFIX.'Estimated Arrival (ETA)',
            ])
            ->delete();

        foreach ([
            ['name' => 'Estimated Departure (ETD)', 'date' => $shipment->etd],
            ['name' => 'Estimated Arrival (ETA)', 'date' => $shipment->eta],
        ] as $schedule) {
            if ($schedule['date']) {
                $shipment->milestones()->create([
                    'milestone_name' => self::SYSTEM_MILESTONE_PREFIX.$schedule['name'],
                    'status' => 'Planned',
                    'planned_date' => $schedule['date']->toDateString(),
                ]);
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function recordCurrentStatuses(Shipments $shipment, array $data): void
    {
        $date = now()->toDateString();

        foreach ([
            ['label' => 'Document status', 'status' => $shipment->status_document],
            ['label' => 'Tracking status', 'status' => $shipment->tracking_status],
        ] as $status) {
            if ($status['status']) {
                $this->recordStatusChange($shipment, $status['label'], $status['status'], $data, $date);
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function recordStatusChange(
        Shipments $shipment,
        string $label,
        ?string $status,
        array $data,
        ?string $date = null,
    ): void {
        if (! $status) {
            return;
        }

        $date ??= now()->toDateString();
        $isTrackingStatus = $label === 'Tracking status';
        $progress = $isTrackingStatus
            ? (preg_match('/complete|delivered|arrived|received/i', $status) ? 'Completed' : (preg_match('/not shipped|planned|pending|scheduled|on hold/i', $status) ? 'Planned' : 'In Progress'))
            : 'Completed';

        $shipment->milestones()->create([
            'milestone_name' => self::SYSTEM_MILESTONE_PREFIX.$label.': '.$status,
            'status' => $progress,
            'planned_date' => $isTrackingStatus && $progress === 'Planned'
                ? $shipment->etd?->toDateString()
                : null,
            'actual_date' => $isTrackingStatus && $progress === 'Planned'
                ? null
                : ($progress === 'Completed' && $isTrackingStatus
                    ? ($shipment->ata?->toDateString() ?? $date)
                    : ($isTrackingStatus ? ($shipment->atd?->toDateString() ?? $date) : $date)),
        ]);

        $shipment->statusHistories()->create([
            'status' => $label.': '.$status,
            'status_date' => $date,
            'location' => $data['location'] ?? null,
            'country' => $data['country'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }
}
