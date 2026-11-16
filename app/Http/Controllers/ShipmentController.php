<?php

namespace App\Http\Controllers;

use App\Contracts\ShipmentServiceInterface;
use App\Http\Requests\ShipmentRequest;
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
        ])->latest()->get();

        return Inertia::render('shipment/index', ['shipments' => $shipments]);
    }

    public function create(): Response
    {
        $purchaseOrders = PurchaseOrders::query()
            ->select('id', 'po_number')
            ->orderBy('po_number')
            ->get();

        return Inertia::render('shipment/create', [
            'shipment_number' => $this->shipmentService->generateShipmentNumber(),
            'purchaseOrders' => $purchaseOrders,
            'purchaseOrderItems' => $this->purchaseOrderItems(),
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
        ]);

        // dd([
        //     'milestones' => $shipment->milestones()->get()->toArray(),
        //     'status_histories' => $shipment->statusHistories()->get()->toArray(),
        // ]);

        return Inertia::render('shipment/show', [
            'shipment' => $shipment,
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

        //     dd([
        //     'database_containers' => $shipment->shipmentContainers->toArray(),
        //     'form_containers' => $this->shipmentContainersForForm($shipment),
        // ]);
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
            'purchaseOrderItems' => $this->purchaseOrderItems($shipment),
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
