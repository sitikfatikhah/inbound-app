<?php

namespace App\Http\Controllers;

use App\Contracts\SupplierServiceInterface;
use App\Http\Requests\SupplierRequest;
use App\Models\Suppliers;
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

        return Inertia::render('supplier/index', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('supplier/create');

    }

    /**
     * Store a newly created supplier.
     */
    public function store(SupplierRequest $request)
    {

        $this->suppliers_service->create(
            $request->validated()
        );

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Suppliers $supplier)
    {
        return Inertia::render('supplier/show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Suppliers $supplier)
    {
        return Inertia::render('supplier/edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified supplier.
     */
    public function update(
        SupplierRequest $request,
        Suppliers $supplier
    ) {
        $this->suppliers_service->update(
            $supplier,
            $request->validated()
        );

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Suppliers $supplier)
    {
        $this->suppliers_service->delete($supplier);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
