<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderItemsRequest;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Products;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $purchaseOrderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $purchaseOrders = PurchaseOrders::with([
            'supplier',
            'items.product',
        ])
            ->latest()
            ->get();

        return Inertia::render('po/index', [
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $suppliers = Suppliers::orderBy('supplier_name')
            ->get([
                'id',
                'supplier_name',
            ]);

        $products = Products::where('is_active', true)
            ->orderBy('product_name')
            ->get([
                'id',
                'product_code',
                'product_name',
                'unit',
                'hs_code',
                'import_duty',
                'facility',
            ]);

        return Inertia::render('po/create', [
            'purchaseOrders' => PurchaseOrders::query()
                ->select('id', 'po_number')
                ->orderBy('po_number')
                ->get(),

            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        PurchaseOrderRequest $request,
        PurchaseOrderItemsRequest $itemsRequest
    ): RedirectResponse {
        $this->purchaseOrderService->create(
            $request->validated(),
            $itemsRequest->validated()
        );

        return redirect()
            ->route('po.index')
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrders $purchaseOrder): Response
    {
        $purchaseOrder->load([
            'supplier',
            'items.product',
        ]);

        return Inertia::render('po/show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrders $purchaseOrder): Response
    {
        $suppliers = Suppliers::orderBy('supplier_name')
            ->get([
                'id',
                'supplier_name',
            ]);

        $products = Products::where('is_active', true)
            ->orderBy('product_name')
            ->get([
                'id',
                'product_code',
                'product_name',
                'unit',
                'hs_code',
                'import_duty',
                'facility',
            ]);

        $purchaseOrder->load([
            'items',
        ]);

        return Inertia::render('po/edit', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PurchaseOrderRequest $request,
        PurchaseOrders $purchaseOrder,
        PurchaseOrderItemsRequest $itemsRequest
    ): RedirectResponse {
        $this->purchaseOrderService->update(
            $purchaseOrder,
            $request->validated(),
            $itemsRequest->validated()
        );

        return redirect()
            ->route('po.index')
            ->with('success', 'Purchase Order berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        PurchaseOrders $purchaseOrder
    ): RedirectResponse {
        $this->purchaseOrderService->delete($purchaseOrder);

        return redirect()
            ->route('po.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }
}
