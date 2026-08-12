<?php

namespace App\Http\Controllers;

use App\Contracts\SupplierServiceInterface;
use App\Models\Suppliers;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierServiceInterface $suppliers_service
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplier = Suppliers::latest()->get();
        return Inertia::render('Supplier/Index',[
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Supplier/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $supplier = $this->Supplier_service->create(
            $request->validated()
        );

        return response()->json($supplier);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchaseOrder = Suppliers::findOrFail($id);
        return Inertia::render('Supplier/Show', [
            'supplier' => $purchaseOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = Suppliers::findOrFail($id);
        return Inertia::render('Supplier/Edit',[
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = $this->suppliers_service->update(
            $supplier,
            $request->validate()
        );
        return response()->json($supplier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Suppliers::findOrFail($id);

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
