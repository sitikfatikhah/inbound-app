<?php

namespace App\Http\Controllers;

use App\Models\WarehouseReceipts;
use App\Models\Warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouse = WarehouseReceipts::latest()->get();

        return Inertia::render('Warehouse/Index', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Warehouse/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => ['required', 'integer'],
            'warehouse_id' => ['required', 'integer'],
            'arrival_number' => ['nullable', 'string'],
            'received_date' =>['nullable', 'date'],
            'status' => ['nullable', 'enum'],
        ]);

        WarehouseReceipts::create('$validated');

        return redirect()
            ->route('warehouse.index')
            ->with('success', 'Warehouse Receipt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $warehouse = WarehouseReceipts::findOrFail($id);
        return Inertia::render('Warehouse/Show',[
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $warehouse = WarehouseReceipts::findOrFail($id);
        
        return Inertia::render('Warehouse/Edit',[
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $warehouse = WarehouseReceipts::findOrFail($id);
        $validated = $request->validate([
            'company_id'    => ['required', 'integer'],
            'po_number'     => ['required', 'string', 'max:100'],
            'supplier_id'   => ['required', 'integer'],
            'po_date'       => ['required', 'date'],
            'currency'      => ['required', 'string', 'max:10'],
            'exchange_rate' => ['nullable', 'numeric'],
            'incoterm'      => ['nullable', 'string', 'max:20'],
            'payment_term'  => ['nullable', 'string', 'max:100'],
            'delivery_date' => ['nullable', 'date'],
            'arrival_date'  => ['nullable', 'date'],
            'status'        => ['required', 'string', 'max:50'],
        ]);

        $warehouse->update($validated);

        return redirect()
            ->route('Warehouse.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = WarehouseReceipts::findOrFail($id);
        $warehouse->delete();

        return redirect()
            ->route('warehouse.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}
