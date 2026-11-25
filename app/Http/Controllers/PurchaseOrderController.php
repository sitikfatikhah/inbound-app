<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Products;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\User;
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
     * Display a listing of purchase orders.
     */
    public function index(): Response
    {
        $purchaseOrders = PurchaseOrders::query()
            ->with([
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
     * Show the form for creating a new purchase order.
     */
    public function create(): Response
    {
        $suppliers = Suppliers::query()
            ->orderBy('supplier_name')
            ->get([
                'id',
                'supplier_name',
            ]);

        $products = Products::query()
            ->where('is_active', true)
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
                ->select([
                    'id',
                    'po_number',
                ])
                ->orderBy('po_number')
                ->get(),

            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(
        PurchaseOrderRequest $request
    ): RedirectResponse {
        $this->purchaseOrderService->create(
            $request->validated()
        );

        return redirect()
            ->route('po.index')
            ->with(
                'success',
                'Purchase Order berhasil dibuat.'
            );
    }

    /**
     * Display the specified purchase order.
     */
    public function show(
        User $user,
        PurchaseOrders $purchaseOrder
    ): Response {
        abort_unless(
            $user->can('purchase-order.view')
        && $purchaseOrder->created_by === $user->id,
        403
        );
        
        $purchaseOrder->load([
            'supplier',
            'items.product',
        ]);
        
        return Inertia::render('po/show', [
            'purchaseOrder' => $purchaseOrder,
            'items' => $purchaseOrder->items,
        ]);
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit(
        PurchaseOrders $purchaseOrder
    ): Response {
        $suppliers = Suppliers::query()
            ->orderBy('supplier_name')
            ->get([
                'id',
                'supplier_name',
            ]);

        $products = Products::query()
            ->where('is_active', true)
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
            'items.product',
        ]);

        return Inertia::render('po/edit', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified purchase order.
     */
    public function update(
        PurchaseOrderRequest $request,
        PurchaseOrders $purchaseOrder
    ): RedirectResponse {
        $this->purchaseOrderService->update(
            $purchaseOrder,
            $request->validated()
        );

        return redirect()
            ->route('po.index')
            ->with(
                'success',
                'Purchase Order berhasil diupdate.'
            );
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy(
        PurchaseOrders $purchaseOrder
    ): RedirectResponse {
        $this->purchaseOrderService->delete(
            $purchaseOrder
        );

        return redirect()
            ->route('po.index')
            ->with(
                'success',
                'Purchase Order berhasil dihapus.'
            );
    }
}
