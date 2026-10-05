<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingLineRequest;
use App\Models\ShippingLines;
use Inertia\Inertia;

class ShippingLineController extends Controller
{
    public function index()
    {
        $shippingLines = ShippingLines::latest()->get();

        return Inertia::render('shippingLine/index', [
            'shippingLines' => $shippingLines,
        ]);
    }

    public function create()
    {
        return Inertia::render('shippingLine/create', [
            'shippingLines' => ShippingLines::query()
                ->select('id', 'shipping_line_name')
                ->orderBy('shipping_line_name')
                ->get(),
        ]);

    }

    public function store(ShippingLineRequest $request)
    {
        ShippingLines::create(
            $request->validated()
        );

        return redirect()
            ->route('shipping_line.index')
            ->with('success', 'shippingLine created successfully.');
    }

    public function show(ShippingLines $shippingLine)
    {
        return Inertia::render('shippingLine/show', [
            'shippingLine' => $shippingLine,
        ]);
    }

    public function edit(ShippingLines $shippingLine)
    {
        return Inertia::render('shippingLine/edit', [
            'shippingLine' => $shippingLine,
        ]);
    }

    /**
     * Update the specified port.
     */
    public function update(ShippingLineRequest $request, ShippingLines $shippingLine)
    {
        dd($request->validated());
        $shippingLine->update($request->validated());

        return redirect()
            ->route('shipping_line.index')
            ->with('success', 'shippingLine updated successfully.');
    }

    /**
     * Remove the specified port.
     */
    public function destroy(ShippingLines $shippingLine)
    {
        $shippingLine->delete();

        return redirect()
            ->route('shipping_line.index')
            ->with('success', 'shippingLine deleted successfully.');
    }
}
