<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Products;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Products::latest()->get();

        return Inertia::render('product/index', [
            'product' => $products,
        ]);
    }

    public function create()
    {
        return Inertia::render('product/create', [
            'product' => Products::query()
                ->select('id', 'product_code')
                ->orderBy('product_code')
                ->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        Products::create(
            $request->validated()
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'products created successfully.');
    }

    public function show(Products $products)
    {
        return Inertia::render('product/show', [
            'product' => $products,
        ]);
    }

    public function edit(Products $products)
    {
        return Inertia::render('product/edit', [
            'product' => $products,
        ]);
    }

    /**
     * Update the specified port.
     */
    public function update(ProductRequest $request, Products $products)
    {
        $products->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified port.
     */
    public function destroy(Products $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'products deleted successfully.');
    }
}
