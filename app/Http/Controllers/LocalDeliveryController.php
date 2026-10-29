<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocalDeliveryRequest;
use App\Models\LocalDelivery;
use App\Models\PurchaseOrders;
use Inertia\Inertia;

class LocalDeliveryController extends Controller
{
    public function index()
    {
        $local_deliveries = LocalDelivery::latest()->get();

        return Inertia::render('local-delivery/index', [
            'local_deliveries' => $local_deliveries,
        ]);
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrders::query()
            ->select('id', 'po_number')
            ->where('category', 'local')
            ->get();

        return Inertia::render('local-delivery/create', [
            'local_deliveries' => LocalDelivery::query(),
            'purchaseOrders' => $purchaseOrders
                ->select('id', 'delivery_name')
                ->orderBy('delivery_name')
                ->get(),
        ]);
    }

    public function store(LocalDeliveryRequest $request)
    {

        LocalDelivery::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Local delivery created successfully.');
    }

    public function show(LocalDelivery $local_delivery)
    {
        return Inertia::render('local-delivery/show', [
            'local_delivery' => $local_delivery,
        ]);
    }

    public function edit(LocalDelivery $local_delivery)
    {

        return Inertia::render('local-delivery/edit', [
            'local_delivery' => $local_delivery,
        ]);

    }

    /**
     * Update the specified port.
     */
    public function update(LocalDeliveryRequest $request, LocalDelivery $local_delivery)
    {
        $local_delivery->update($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Local delivery updated successfully.');
    }

    /**
     * Remove the specified port.
     */
    public function destroy(LocalDelivery $local_delivery)
    {
        $local_delivery->delete();

        return redirect()
            ->back()
            ->with('success', 'Local delivery deleted successfully.');
    }
}
