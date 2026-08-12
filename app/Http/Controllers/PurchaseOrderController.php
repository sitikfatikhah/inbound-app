<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrders;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrders::latest()->get();

        return Inertia::render('PurchaseOrder/Index', [
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('PurchaseOrder/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $custom = $this->purchaseOrder_service->create(
            $request->validated()
        );

        return response()->json($custom);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchaseOrder = PurchaseOrders::findOrFail($id);

        return Inertia::render('PurchaseOrder/Show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchaseOrder = PurchaseOrders::findOrFail($id);

        return Inertia::render('PurchaseOrder/Edit', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, purchaseOrder $purchaseOrder)
    {
        $purchaseOrder = $this->purchaseOrder_service->create(
            $request->validated()
        );

        return response()->json($custom);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->purchaseOrder_service->delete($purchaseOrder);
        return response()->json([
            'message' => 'Customs deleted successfully.'
        ]);
    }
}
