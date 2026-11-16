<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocalShipmentRequest;
use App\Models\LocalShipments;
use App\Models\PurchaseOrderItems;
use App\Models\PurchaseOrders;
use App\Models\TrackingStatus;
use App\Models\Warehouses;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LocalShipmentsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('local-shipment/index', [
            'localShipments' => LocalShipments::query()
                ->with([
                    'purchaseOrder:id,po_number,supplier_id',
                    'purchaseOrder.supplier:id,supplier_name',
                    'purchaseOrders',
                    'purchaseOrders.supplier:id,supplier_name',
                    'warehouse:id,warehouse_name',
                ])
                ->withCount('statusHistories')
                ->latest()
                ->get(),
        ]);
    }

    public function create(): Response
    {
        $prefix = 'LOCSHP-'.now()->format('Ymd').'-';

        $latestShipmentNumber = LocalShipments::query()
            ->where('shipment_number', 'like', $prefix.'%')
            ->latest('id')
            ->value('shipment_number');
        $nextNumber = $latestShipmentNumber
            ? (int) substr($latestShipmentNumber, -4) + 1
            : 1;
        $shipmentNumber = $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

        return Inertia::render('local-shipment/create', [
            'shipment_number' => $shipmentNumber,
            ...$this->lookupOptions(),
            'purchaseOrderItems' => $this->purchaseOrderItems(),
        ]);
    }

    public function store(LocalShipmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);
        $data['purchase_order_id'] = $items[0]['purchase_order_id'];


        DB::transaction(function () use ($data, $items): void {
            $shipment = LocalShipments::query()->create($data);
            $this->recordStatusChanges($shipment);
            $this->syncItems($shipment, $items);
        });

        return redirect()->route('local-shipment.index')
            ->with('success', 'Local Shipment created successfully.');
    }

    public function show(LocalShipments $local_shipment): Response
    {
        $local_shipment->load([
            'purchaseOrder:id,po_number,supplier_id',
            'purchaseOrder.supplier:id,supplier_name',
            'purchaseOrders',
            'purchaseOrders.supplier:id,supplier_name',
            'items.purchaseOrderItem',
            'warehouse:id,warehouse_name',
            'statusHistories',
        ]);
        dd($local_shipment->items->map(fn ($item): array => [
            'purchase_order_id' => $item->purchase_order_id,
            'purchase_order_item_id' => $item->purchase_order_item_id,
            'product_code' => $item->purchaseOrderItem->product_code,
            'product_name' => $item->purchaseOrderItem->product_name,
            'product_id' => $item->purchaseOrderItem->product_id,
            'quantity' => (float) $item->quantity,
            'unit' => $item->purchaseOrderItem->unit,
        ])->values());
        return Inertia::render('local-shipment/show', [
            'localShipment' => $local_shipment,
        ]);
    }

    public function edit(LocalShipments $local_shipment): Response
    {
        $local_shipment->load([
            'purchaseOrder:id,po_number,supplier_id',
            'purchaseOrder.supplier:id,supplier_name',
            'purchaseOrders',
            'purchaseOrders.supplier:id,supplier_name',
            'items.purchaseOrderItem',
            'warehouse:id,warehouse_name',
        ]);

        return Inertia::render('local-shipment/edit', [
            'localShipment' => [
                ...$local_shipment->toArray(),
                'etd' => $local_shipment->etd?->format('Y-m-d'),
                'atd' => $local_shipment->atd?->format('Y-m-d'),
            ],
            'items' => $local_shipment->items->map(fn ($item): array => [
                'purchase_order_id' => $item->purchase_order_id,
                'purchase_order_item_id' => $item->purchase_order_item_id,
                'product_code' => $item->purchaseOrderItem->product_code,
                'product_name' => $item->purchaseOrderItem->product_name,
                'product_id' => $item->purchaseOrderItem->product_id,
                'quantity' => (float) $item->quantity,
                'unit' => $item->purchaseOrderItem->unit,
            ])->values(),
            'purchaseOrderItems' => $this->purchaseOrderItems($local_shipment),
            ...$this->lookupOptions(),
        ]);
        dd($local_shipment);
    }

    public function update(LocalShipmentRequest $request, LocalShipments $local_shipment): RedirectResponse
    {
        $data = $request->validated();
        $hasItems = array_key_exists('items', $data);
        $items = $data['items'] ?? [];
        unset($data['items']);

        if ($hasItems) {
            $data['purchase_order_id'] = $items[0]['purchase_order_id'];
        }

        $previousDocumentStatus = $local_shipment->status_document;
        $previousTrackingStatus = $local_shipment->tracking_status;

        DB::transaction(function () use ($data, $hasItems, $items, $local_shipment, $previousDocumentStatus, $previousTrackingStatus): void {
            $local_shipment->update($data);
            $this->recordStatusChanges($local_shipment, $previousDocumentStatus, $previousTrackingStatus);

            if ($hasItems) {
                $this->syncItems($local_shipment, $items);
            }
        });

        return redirect()->route('local-shipment.index')
            ->with('success', 'Local Shipment updated successfully.');
    }

    public function destroy(LocalShipments $local_shipment): RedirectResponse
    {
        DB::transaction(function () use ($local_shipment): void {
            $this->restoreItems($local_shipment);
            $local_shipment->delete();
        });

        return redirect()->route('local-shipment.index')
            ->with('success', 'Local Shipment deleted successfully.');
    }

    /** @return array{purchaseOrders: Collection<int, PurchaseOrders>, warehouses: Collection<int, Warehouses>, status_document: Collection<int, TrackingStatus>, tracking_status: Collection<int, TrackingStatus>} */
    private function lookupOptions(): array
    {
        return [
            'purchaseOrders' => PurchaseOrders::query()
                ->whereRaw('LOWER(category) = ?', ['local'])
                ->select('id', 'po_number', 'supplier_id')
                ->with('supplier:id,supplier_name')
                ->orderBy('po_number')
                ->get(),
            'warehouses' => Warehouses::query()
                ->select('id', 'warehouse_code', 'warehouse_name')
                ->orderBy('warehouse_name')
                ->get(),
            'status_document' => TrackingStatus::query()
                ->where('category', 'local-document')
                ->where('is_active', true)
                ->select('id', 'category', 'name')
                ->orderBy('name')
                ->get(),
            'tracking_status' => TrackingStatus::query()
                ->where('category', 'local-shipment')
                ->where('is_active', true)
                ->select('id', 'category', 'name')
                ->orderBy('name')
                ->get(),
        ];
    }

    /** @return Collection<int, PurchaseOrderItems> */
    private function purchaseOrderItems(?LocalShipments $shipment = null): Collection
    {
        $purchaseOrderItems = PurchaseOrderItems::query()
            ->whereHas('purchaseOrder', fn ($query) => $query->whereRaw('LOWER(category) = ?', ['local']))
            ->select('id', 'purchase_order_id', 'product_id', 'product_code', 'product_name', 'quantity', 'remaining_qty', 'unit')
            ->orderBy('product_name')
            ->get();

        if (! $shipment) {
            return $purchaseOrderItems;
        }

        $allocatedQuantities = $shipment->items
            ->groupBy('purchase_order_item_id')
            ->map(static fn (SupportCollection $items): float => (float) $items->sum('quantity'));

        return $purchaseOrderItems->each(static function (PurchaseOrderItems $item) use ($allocatedQuantities): void {
            $allocatedQuantity = (float) $allocatedQuantities->get($item->id, 0);

            if ($allocatedQuantity > 0) {
                $item->remaining_qty = min(
                    (float) ($item->quantity ?? 0),
                    (float) ($item->remaining_qty ?? 0) + $allocatedQuantity,
                );
            }
        });
    }

    /** @param array<int, array{purchase_order_id: int, purchase_order_item_id: int, quantity: float|int}> $items */
    private function syncItems(LocalShipments $shipment, array $items): void
    {
        $this->restoreItems($shipment);

        $quantities = collect($items)
            ->groupBy('purchase_order_item_id')
            ->map(static fn (SupportCollection $allocations): float => (float) $allocations->sum('quantity'));
        $firstIndexByItem = collect($items)
            ->keys()
            ->mapWithKeys(fn (int $index): array => [$items[$index]['purchase_order_item_id'] => $index]);
        $purchaseOrderItems = PurchaseOrderItems::query()
            ->whereKey($quantities->keys())
            ->with('purchaseOrder:id,category')
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items as $index => $allocation) {
            $purchaseOrderItem = $purchaseOrderItems->get($allocation['purchase_order_item_id']);

            if (
                ! $purchaseOrderItem
                || (int) $purchaseOrderItem->purchase_order_id !== (int) $allocation['purchase_order_id']
                || strtolower((string) $purchaseOrderItem->purchaseOrder?->category) !== 'local'
            ) {
                throw ValidationException::withMessages([
                    "items.$index.purchase_order_item_id" => 'The selected PO item does not belong to the selected local purchase order.',
                ]);
            }
        }

        foreach ($quantities as $itemId => $quantity) {
            $purchaseOrderItem = $purchaseOrderItems->get($itemId);
            $availableQuantity = max(0, (float) ($purchaseOrderItem->remaining_qty ?? $purchaseOrderItem->quantity ?? 0));

            if ($quantity > $availableQuantity) {
                $index = $firstIndexByItem->get($itemId);

                throw ValidationException::withMessages([
                    "items.$index.quantity" => 'The allocated quantity exceeds the remaining PO quantity.',
                ]);
            }

            $purchaseOrderItem->update([
                'remaining_qty' => $availableQuantity - $quantity,
                'total_allocated' => (float) ($purchaseOrderItem->total_allocated ?? 0) + $quantity,
            ]);

            $shipment->items()->create([
                'purchase_order_id' => $purchaseOrderItem->purchase_order_id,
                'purchase_order_item_id' => $purchaseOrderItem->id,
                'quantity' => $quantity,
            ]);
        }
    }

    private function restoreItems(LocalShipments $shipment): void
    {
        $allocations = $shipment->items()->lockForUpdate()->get();

        if ($allocations->isEmpty()) {
            return;
        }

        $quantityByItem = $allocations->groupBy('purchase_order_item_id')
            ->map(static fn (SupportCollection $items): float => (float) $items->sum('quantity'));
        $purchaseOrderItems = PurchaseOrderItems::query()
            ->whereKey($quantityByItem->keys())
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($quantityByItem as $itemId => $quantity) {
            $purchaseOrderItem = $purchaseOrderItems->get($itemId);

            if (! $purchaseOrderItem) {
                continue;
            }

            $purchaseOrderItem->update([
                'remaining_qty' => min(
                    (float) ($purchaseOrderItem->quantity ?? 0),
                    (float) ($purchaseOrderItem->remaining_qty ?? 0) + $quantity
                ),
                'total_allocated' => max(0, (float) ($purchaseOrderItem->total_allocated ?? 0) - $quantity),
            ]);
        }

        $shipment->items()->delete();
    }

    private function recordStatusChanges(
        LocalShipments $shipment,
        ?string $previousDocumentStatus = null,
        ?string $previousTrackingStatus = null,
    ): void {
        $statusDate = now();

        foreach ([
            [
                'type' => 'document',
                'status' => $shipment->status_document,
                'previous' => $previousDocumentStatus,
            ],
            [
                'type' => 'shipment',
                'status' => $shipment->tracking_status,
                'previous' => $previousTrackingStatus,
            ],
        ] as $status) {
            if (! $status['status'] || $status['status'] === $status['previous']) {
                continue;
            }

            $shipment->statusHistories()->create([
                'status_type' => $status['type'],
                'status' => $status['status'],
                'status_date' => $statusDate,
            ]);
        }
    }
}
