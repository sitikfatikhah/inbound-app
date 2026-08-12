<?php

namespace App\Http\Controllers;

use App\Models\CustomsClearances;
use App\Models\CustomsPayments;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomsPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customPayment = CustomsPayments::latest()->get();
        return Inertia::render('customPayment/Index', [
            'customPayment' => $customPayment,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CustomsPayment/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customPayment = $this->customPayments_service->create(
            $request->validated()
        );

        return response()->json($customPayment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customPayment = CustomsPayments::findOrFail($id);

        return Inertia::render('CustomPayment/Show', [
            'customsPayment' => $customPayment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customPayment = CustomsPayments::findOrFail($id);

        return Inertia::render('CustomPayment/Edit', [
            'customsPayment' => $customPayment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customPayment = CustomsPayments::findOrFail($id);
        $validated->validate([
            'customs_clearance_id' => ['required', 'integer'],
            'customs_value' => ['nullable', 'decimal'],
            'import_duty' => ['nullable', 'decimal'],
            'import_value' => ['nullable', 'decimal'],
            'vat' => ['nullable', 'decimal'],
            'pph_22_import' => ['nullable', 'decimal'],
            'luxury_tax' => ['nullable', 'decimal'],
            'other_taxes' => ['nullable', 'decimal'],
            'total_import_duty_and_taxes' => ['nullable', 'decimal'],
            'payment_date' => ['nullable', 'date'],
            'billing_code' => ['nullable', 'string'],
        ]);

        $customPayment->update($validated);
        return redirect()
            ->route('CustomPayment.index')
            ->with('success', 'Custom Payment updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customPayment = CustomsPayments::findOrFail($id);
        $customPayment->delete();
        return redirect()
            ->route('CustomPayment.index')
            ->with('success', 'Custom Payment deleted successfully.');
    }
}
