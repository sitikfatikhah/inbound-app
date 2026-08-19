<?php

namespace App\Http\Controllers;

use App\Contracts\ShipmentServiceInterface;
use App\Http\Requests\ShipmentRequest;
use App\Models\Shipments;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentServiceInterface $shipmentService
    ) {}

    /**
     * Display a listing of shipments.
     */
    public function index()
    {
        $shipments = Shipments::latest()->get();

        return Inertia::render('shipment/index', [
            'shipments' => $shipments,
        ]);
    }

    /**
     * Show the form for creating a new shipment.
     */
    public function create()
    {
        return Inertia::render('shipment/create');
    }

    /**
     * Store a newly created shipment.
     */
    public function store(ShipmentRequest $request)
    {
        $shipment = $this->shipmentService->create(
            $request->validated()
        );

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment created successfully.');
    }

    /**
     * Display the specified shipment.
     */
    public function show(Shipments $shipment)
    {
        return Inertia::render('shipment/show', [
            'shipment' => $shipment,
        ]);
    }

    /**
     * Show the form for editing the specified shipment.
     */
    public function edit(Shipments $shipment)
    {
        return Inertia::render('shipment/edit', [
            'shipment' => $shipment,
        ]);
    }

    /**
     * Update the specified shipment.
     */
    public function update(
        ShipmentRequest $request,
        Shipments $shipment
    ) {
        $this->shipmentService->update(
            $shipment,
            $request->validated()
        );

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment updated successfully.');
    }

    /**
     * Remove the specified shipment.
     */
    public function destroy(Shipments $shipment)
    {
        $this->shipmentService->delete($shipment);

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }
}