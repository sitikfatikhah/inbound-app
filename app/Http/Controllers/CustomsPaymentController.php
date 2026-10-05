<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomsPaymentRequest;
use App\Models\CustomsPayments;
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
    public function store(CustomsPaymentRequest $request)
    {
        CustomsPayments::create($request->validated());

        return redirect()
            ->route('payment.index')
            ->with('success', 'Custom payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomsPayments $payment)
    {
        return Inertia::render('CustomPayment/Show', [
            'customsPayment' => $payment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomsPayments $payment)
    {
        return Inertia::render('CustomPayment/Edit', [
            'customsPayment' => $payment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomsPaymentRequest $request, CustomsPayments $payment)
    {
        $payment->update($request->validated());

        return redirect()
            ->route('payment.index')
            ->with('success', 'Custom Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomsPayments $payment)
    {
        $payment->delete();

        return redirect()
            ->route('payment.index')
            ->with('success', 'Custom Payment deleted successfully.');
    }
}
