<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortRequest;
use App\Models\Ports;
use Inertia\Inertia;

class PortController extends Controller
{
    public function index()
    {
        $ports = Ports::latest()->get();

        return Inertia::render('port/index', [
            'ports' => $ports,
        ]);
    }

    public function create()
    {
        return Inertia::render('port/create', [
            'ports' => Ports::query()
                ->select('id', 'port_name')
                ->orderBy('port_name')
                ->get(),
        ]);
    }

    public function store(PortRequest $request)
    {

        Ports::create($request->validated());

        return redirect()
            ->route('ports.index')
            ->with('success', 'Port created successfully.');
    }

    public function show(Ports $port)
    {
        return Inertia::render('port/show', [
            'port' => $port,
        ]);
    }

    public function edit(Ports $port)
    {

        return Inertia::render('port/edit', [
            'port' => $port,
        ]);

    }

    /**
     * Update the specified port.
     */
    public function update(PortRequest $request, Ports $port)
    {
        $port->update($request->validated());

        return redirect()
            ->route('ports.index')
            ->with('success', 'Port updated successfully.');
    }

    /**
     * Remove the specified port.
     */
    public function destroy(Ports $port)
    {
        $port->delete();

        return redirect()
            ->route('ports.index')
            ->with('success', 'Port deleted successfully.');
    }
}
