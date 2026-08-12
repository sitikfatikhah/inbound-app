<?php

namespace App\Http\Controllers;

use App\Models\Containers;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $container = Containers::latest()->get();
        return Inertia::render('Container/Index', [
            'containers' => $container,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Container/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validated([
            'shipment_id' => ['required', 'integer'],
            'container_number' => ['required', 'string'],
            'container_size'=> ['required', 'string'],
            'seal_number' => ['nullable', 'string'],
            'gross_weight' => ['nullable', 'decimal'],
            'net_weight' => ['nullable', 'decimal'],
            'cbm' => ['nullable', 'decimal'],
            'status' => ['nullable', 'enum'],
        ]);
        Containers::created($validated);

        return redirect()
            ->route('container.index')
            ->with('success', 'Container created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $container = Containers::findOrFail($id);
        return Inertia::render('Container/Show', [
            'container' => $container,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $container = Containers::findOrFail($id);
        return Inertia::render('Container/Edit', [
            'container' => $container,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $container = Containers::findOrFail($id);

        $validated = $request->validated([
            'shipment_id' => ['required', 'integer'],
            'container_number' => ['required', 'string'],
            'container_size'=> ['required', 'string'],
            'seal_number' => ['nullable', 'string'],
            'gross_weight' => ['nullable', 'decimal'],
            'net_weight' => ['nullable', 'decimal'],
            'cbm' => ['nullable', 'decimal'],
            'status' => ['nullable', 'enum'],
        ]);
        $container->DB::update($validated);

        return redirect()
            ->route('container.index')
            ->with('success', 'Container updated sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $container = Containers::findOrFail($id);

        $container->delete();
        return redirect()
            ->route('containers.index')
            ->with('success', 'Container deleted successfully.');
    }
}
