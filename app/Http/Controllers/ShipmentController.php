<?php

namespace App\Http\Controllers;

use App\Contracts\ShipmentServiceInterface;
use App\Http\Requests\ShipmentRequest;
use App\Models\shipments;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentServiceInterface $shipmentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShipmentRequest $request)
    {
        $shipment = $this->shipmentService->create(
            $request->validated()
        );

        return response()->json($shipment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShipmentRequest $request, shipments $shipment)
    {
        $shipment = $this->shipmentService->update(
            $shipment,
            $request->validated()
        );

        return response()->json($shipment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->shipmentService->delete($shipment);

        return response()->json([
            'message' => 'Shipment deleted successfully.',
        ]);
    }
}
