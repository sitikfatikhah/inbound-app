<?php

namespace App\Http\Controllers;

use App\Contracts\ShipmentServiceInterface;
use App\Http\Requests\ShipmentRequest;
use App\Models\Forwarders;
use App\Models\Ports;
use App\Models\Shipments;
use App\Models\ShippingLines;
use App\Models\Warehouses;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentServiceInterface $shipmentService
    ) {}

    /**
     * Display a listing of shipments.
     */
    public function index(): Response
    {
        $shipments = Shipments::with([
            'forwarder:id,forwarder_name',
            'shippingLine:id,shipping_line_name',
            'portOfLoading:id,port_name',
            'portOfDischarge:id,port_name',
            'warehouse:id,warehouse_name',
        ])->latest()->get();

        return Inertia::render('shipment/index', [
            'shipments' => $shipments,
        ]);
    }

    /**
     * Show the form for creating a new shipment.
     */
    public function create(): Response
    {
        return Inertia::render('shipment/create', [
            'shipment_number' => $this->shipmentService->generateShipmentNumber(),
            ...$this->lookupOptions(),
        ]);
    }

    /**
     * Store a newly created shipment.
     */
    public function store(ShipmentRequest $request): RedirectResponse
    {
        $this->shipmentService->create($request->validated());

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment created successfully.');
    }

    /**
     * Display the specified shipment.
     */
    public function show(Shipments $shipment): Response
    {
        $shipment->load([
            'forwarder:id,forwarder_name',
            'shippingLine:id,shipping_line_name',
            'portOfLoading:id,port_name',
            'portOfDischarge:id,port_name',
            'warehouse:id,warehouse_name',
            'milestones' => fn ($query) => $query->orderBy('planned_date'),
            'statusHistories',
        ]);

        return Inertia::render('shipment/show', [
            'shipment' => $shipment,
        ]);
    }

    /**
     * Show the form for editing the specified shipment.
     */
    public function edit(Shipments $shipment): Response
    {
        return Inertia::render('shipment/edit', [
            'shipment' => $shipment->load([
                'forwarder:id,forwarder_name',
                'shippingLine:id,shipping_line_name',
                'portOfLoading:id,port_name',
                'portOfDischarge:id,port_name',
                'warehouse:id,warehouse_name',
            ]),
            ...$this->lookupOptions(),
        ]);
    }

    /**
     * Update the specified shipment.
     */
    public function update(
        ShipmentRequest $request,
        Shipments $shipment
    ): RedirectResponse {
        $this->shipmentService->update($shipment, $request->validated());

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment updated successfully.');
    }

    /**
     * Remove the specified shipment.
     */
    public function destroy(Shipments $shipment): RedirectResponse
    {
        $this->shipmentService->delete($shipment);

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function lookupOptions(): array
    {
        return [
            'forwarders' => Forwarders::query()
                ->select('id', 'forwarder_name')
                ->orderBy('forwarder_name')
                ->get(),
            'warehouses' => Warehouses::query()
                ->select('id', 'warehouse_name')
                ->orderBy('warehouse_name')
                ->get(),
            'shippingLines' => ShippingLines::query()
                ->select('id', 'shipping_line_name')
                ->orderBy('shipping_line_name')
                ->get(),
            'ports' => Ports::query()
                ->select('id', 'port_name')
                ->orderBy('port_name')
                ->get(),
        ];
    }
}
