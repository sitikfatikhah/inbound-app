<?php

namespace App\Http\Controllers;

use App\Contracts\ShipmentServiceInterface;
use App\Http\Requests\ShipmentRequest;
use App\Http\Requests\UpdateShipmentTrackingStatusRequest;
use App\Models\Forwarders;
use App\Models\Ports;
use App\Models\PurchaseOrderItems;
use App\Models\PurchaseOrders;
use App\Models\Shipments;
use App\Models\ShippingLines;
use App\Models\Warehouses;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentServiceInterface $shipmentService
    ) {}

    public function index(): Response
    {
        $shipments = Shipments::with([
            'forwarder:id,forwarder_name',
            'purchaseOrders:id,po_number',
            'shippingLine:id,shipping_line_name',
            'portOfLoading:id,port_name',
            'portOfDischarge:id,port_name',
            'warehouse:id,warehouse_name',
            'shipmentDocuments',
            'customsClearance',
            'scheduleHistories',
            'warehouseReceipts',
        ])->latest()->get();

        return Inertia::render('shipment/index', [
            'shipments' => $shipments,
            ...$this->shipmentDashboardData($shipments),
        ]);
    }

    public function create(): Response
    {
        $purchaseOrders = PurchaseOrders::query()
            ->select('id', 'po_number')
            ->with('supplier:id,supplier_name')
            ->whereHas('items', function ($query) {
                $query->where('remaining_qty', '>', 0);
            })
            ->where('category', 'IMPORT')
            ->orderBy('po_number')
            ->get();

        return Inertia::render('shipment/create', [
            'shipment_number' => $this->shipmentService->generateShipmentNumber(),
            'purchaseOrders' => $purchaseOrders,
            'items' => $this->purchaseOrderItems(),
            'documentTypes' => config('documents.types', []),
            'requiredDocumentTypes' => config('documents.required_by_type.import', []),
            ...$this->lookupOptions(),
        ]);
    }

    public function store(ShipmentRequest $request): RedirectResponse
    {
        $this->shipmentService->create($request->validated());

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment created successfully.');
    }

    public function show(Shipments $shipment): Response
    {
        $shipment->load([
            'forwarder:id,forwarder_name',
            'shippingLine:id,shipping_line_name',
            'portOfLoading:id,port_name',
            'portOfDischarge:id,port_name',
            'warehouse:id,warehouse_name',
            'purchaseOrders:id,po_number',
            'shipmentContainers.purchaseOrderItem:id,product_code,product_name,unit',
            'milestones',
            'statusHistories',
            'shipmentDocuments',
            'customsClearance',
            'scheduleHistories.updater:id,name',
        ]);
        $shipment->shipmentDocuments->each(
            fn ($document) => $document->setAttribute(
                'document_url',
                $document->document_file ? Storage::disk('public')->url($document->document_file) : null,
            ),
        );

        return Inertia::render('shipment/show', [
            'shipment' => $shipment,
            'documentTypes' => config('documents.types', []),
            'requiredDocumentTypes' => config('documents.required_by_type.import', []),
            'inspectionSlaHours' => (int) config('documents.inspection_sla_hours', 24),
        ]);
    }

    public function edit(Shipments $shipment): Response
    {
        $shipment->load([
            'forwarder:id,forwarder_name',
            'shippingLine:id,shipping_line_name',
            'portOfLoading:id,port_name',
            'portOfDischarge:id,port_name',
            'warehouse:id,warehouse_name',
            'purchaseOrders:id,po_number',
            'shipmentContainers',
            'milestones' => fn ($query) => $query->orderBy('planned_date')->orderBy('id'),
        ]);

        // Pastikan tanggal sesuai format input type="date" pada Vue.
        $formatDate = static function ($date): ?string {
            return $date
                ? Carbon::parse($date)->format('Y-m-d')
                : null;
        };

        $shipmentData = $shipment->toArray();

        $shipmentData['etd'] = $formatDate($shipment->etd);
        $shipmentData['eta'] = $formatDate($shipment->eta);
        $shipmentData['atd'] = $formatDate($shipment->atd);
        $shipmentData['ata'] = $formatDate($shipment->ata);

        return Inertia::render('shipment/edit', [
            'shipment' => $shipmentData,
            'containers' => $this->shipmentContainersForForm($shipment),
            'milestones' => $shipment->milestones
                ->reject(fn ($milestone): bool => str_starts_with($milestone->milestone_name, '[System] '))
                ->map(fn ($milestone): array => [
                    'milestone_name' => $milestone->milestone_name,
                    'status' => $milestone->status,
                    'planned_date' => $milestone->planned_date?->format('Y-m-d'),
                    'actual_date' => $milestone->actual_date?->format('Y-m-d'),
                    'remarks' => $milestone->remarks,
                ])->values(),
            'items' => $this->purchaseOrderItems($shipment),
            ...$this->lookupOptions(),
        ]);

    }

    public function update(ShipmentRequest $request, Shipments $shipment): RedirectResponse
    {
        $this->shipmentService->update($shipment, $request->validated());

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment updated successfully.');
    }

    public function destroy(Shipments $shipment): RedirectResponse
    {
        $this->shipmentService->delete($shipment);

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }

    public function updateTrackingStatus(
        UpdateShipmentTrackingStatusRequest $request,
        Shipments $shipment,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($shipment, $validated): void {
            $shipment->update([
                'tracking_status' => $validated['tracking_status'],
            ]);

            $shipment->statusHistories()->create([
                'status' => 'Tracking status: '.$validated['tracking_status'],
                'status_date' => $validated['status_date'] ?? now()->toDateString(),
                'location' => $validated['location'] ?? null,
                'country' => $validated['country'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);
        });

        return back()->with('success', 'Shipment tracking status updated.');
    }

    /** @return array{insights: array<string, mixed>, notifications: array<int, array<string, mixed>>} */
    private function shipmentDashboardData(Collection $shipments): array
    {
        $requiredTypes = config('documents.required_by_type.import', []);
        $completeShipments = $shipments->filter(function (Shipments $shipment) use ($requiredTypes): bool {
            $documents = $shipment->shipmentDocuments->keyBy('document_type');

            return $requiredTypes !== [] && collect($requiredTypes)->every(
                fn (string $type): bool => $documents->get($type)?->verification_status === 'complete',
            );
        });

        $inspectionDurations = $shipments
            ->filter(fn (Shipments $shipment): bool => $shipment->customsClearance?->jalur === 'Merah')
            ->map(fn (Shipments $shipment): ?int => $shipment->customsClearance?->inspection_duration)
            ->filter(fn (?int $duration): bool => $duration !== null);
        $clearanceLeadTimes = $shipments
            ->map(function (Shipments $shipment): ?int {
                $clearance = $shipment->customsClearance;

                if (! $clearance?->pib_date || ! $clearance->sppb_date) {
                    return null;
                }

                return (int) Carbon::parse($clearance->pib_date)
                    ->diffInDays(Carbon::parse($clearance->sppb_date), false);
            })
            ->filter(fn (?int $duration): bool => $duration !== null);
        $etaRevisionCounts = $shipments->map(
            fn (Shipments $shipment): int => $shipment->scheduleHistories
                ->where('schedule_type', 'eta')
                ->count(),
        );
        $scheduleDeviations = $shipments
            ->map(function (Shipments $shipment): ?int {
                if (! $shipment->original_eta || ! $shipment->ata) {
                    return null;
                }

                return (int) Carbon::parse($shipment->original_eta)
                    ->diffInDays(Carbon::parse($shipment->ata), false);
            })
            ->filter(fn (?int $deviation): bool => $deviation !== null);
        $delayReasons = $shipments
            ->flatMap(fn (Shipments $shipment) => $shipment->scheduleHistories->pluck('revision_reason'))
            ->filter()
            ->countBy()
            ->sortDesc();
        $notifications = [];
        $inspectionSlaHours = (int) config('documents.inspection_sla_hours', 24);

        foreach ($shipments as $shipment) {
            $clearance = $shipment->customsClearance;

            if ($clearance?->jalur === 'Merah') {
                $notifications[] = [
                    'type' => 'critical',
                    'shipment_id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'message' => 'Shipment mendapat Jalur Merah.',
                ];

                if (
                    $clearance->inspection_start
                    && ! $clearance->inspection_end
                    && Carbon::parse($clearance->inspection_start)->lt(now()->subHours($inspectionSlaHours))
                ) {
                    $notifications[] = [
                        'type' => 'warning',
                        'shipment_id' => $shipment->id,
                        'shipment_number' => $shipment->shipment_number,
                        'message' => "Pemeriksaan belum selesai melewati target {$inspectionSlaHours} jam.",
                    ];
                }
            }

            $etaRevisions = $shipment->scheduleHistories
                ->where('schedule_type', 'eta')
                ->count();

            if ($etaRevisions > 2) {
                $notifications[] = [
                    'type' => 'warning',
                    'shipment_id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'message' => 'ETA berubah lebih dari dua kali.',
                ];
            }

            $hasReceivedReceipt = $shipment->warehouseReceipts->contains(
                fn ($receipt): bool => $receipt->status === 'Received' || $receipt->received_date !== null,
            );

            if ($shipment->eta && $shipment->eta->lt(today()) && ! $hasReceivedReceipt) {
                $notifications[] = [
                    'type' => 'critical',
                    'shipment_id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'message' => 'ETA terlewati tetapi barang belum diterima gudang.',
                ];
            }

            if ($completeShipments->contains('id', $shipment->id)) {
                $notifications[] = [
                    'type' => 'complete',
                    'shipment_id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'message' => 'Dokumen wajib sudah lengkap.',
                ];
            }
        }

        return [
            'insights' => [
                'average_inspection_duration_hours' => $inspectionDurations->isEmpty()
                    ? 0
                    : round($inspectionDurations->avg() / 60, 1),
                'document_completeness_percentage' => $shipments->isEmpty()
                    ? 0
                    : round($completeShipments->count() / $shipments->count() * 100, 1),
                'average_eta_revision_frequency' => $shipments->isEmpty()
                    ? 0
                    : round($etaRevisionCounts->sum() / $shipments->count(), 1),
                'average_schedule_deviation_days' => $scheduleDeviations->isEmpty()
                    ? 0
                    : round($scheduleDeviations->avg(), 1),
                'average_customs_clearance_lead_time_days' => $clearanceLeadTimes->isEmpty()
                    ? 0
                    : round($clearanceLeadTimes->avg(), 1),
                'most_common_delay_reason' => $delayReasons->isEmpty()
                    ? null
                    : [
                        'reason' => $delayReasons->keys()->first(),
                        'count' => $delayReasons->first(),
                    ],
            ],
            'notifications' => $notifications,
        ];
    }

    /** @return array<string, mixed> */
    private function lookupOptions(): array
    {
        return [
            'forwarders' => Forwarders::query()->select('id', 'forwarder_name')->orderBy('forwarder_name')->get(),
            'warehouses' => Warehouses::query()->select('id', 'warehouse_name')->orderBy('warehouse_name')->get(),
            'shippingLines' => ShippingLines::query()->select('id', 'shipping_line_name')->orderBy('shipping_line_name')->get(),
            'ports' => Ports::query()->select('id', 'port_name')->orderBy('port_name')->get(),
            'purchaseOrders' => PurchaseOrders::query()->select('id', 'po_number')->orderBy('po_number')->get(),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    private function purchaseOrderItems(?Shipments $shipment = null): Collection
    {
        $allocatedInShipment = $shipment?->shipmentContainers
            ->groupBy('purchase_order_item_id')
            ->map(static fn (Collection $allocations): float => (float) $allocations->sum('quantity'))
            ?? collect();

        return collect($this->shipmentService->getPurchaseOrderItems())
            ->map(static function (array $item) use ($allocatedInShipment): array {
                $item['id'] = (int) $item['id'];
                $item['purchase_order_id'] = (int) $item['purchase_order_id'];
                $item['remaining_qty'] = max(
                    0,
                    (float) ($item['remaining_qty'] ?? 0)
                        + (float) $allocatedInShipment->get($item['id'], 0)
                );

                return $item;
            })
            ->values();
    }

    /** @return array<int, array{container_number: ?string, container_seal_number: ?string, items: array<int, array{purchase_order_item_id: int, quantity: float}>}> */
    private function shipmentContainersForForm(Shipments $shipment): array
    {
        return $shipment->shipmentContainers
            ->groupBy(function ($container) {
                // Record yang belum memiliki nomor kontainer
                // dikelompokkan berdasarkan ID record.
                return $container->container_number
                    ?: 'container-'.$container->id;
            })
            ->map(function ($rows) {
                $first = $rows->first();

                return [
                    'id' => $first->id,
                    'container_number' => $first->container_number ?? '',
                    'container_seal_number' => $first->seal_number ?? '',
                    'items' => $rows->map(function ($row) {
                        return [
                            'purchase_order_id' => $row->purchase_order_item_id
                                    ? PurchaseOrderItems::query()
                                        ->whereKey($row->purchase_order_item_id)
                                        ->value('purchase_order_id')
                                    : null,
                            'purchase_order_item_id' => $row->purchase_order_item_id,
                            'quantity' => $row->quantity,
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }
}
