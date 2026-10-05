<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForwarderRequest;
use App\Models\Forwarders;
use Inertia\Inertia;

class ForwarderController extends Controller
{
    /**
     * Display a listing of the forwarders.
     */
    public function index()
    {
        $forwarders = Forwarders::latest()->get();

        return Inertia::render('forwarder/index', [
            'forwarders' => $forwarders,
        ]);
    }

    /**
     * Show the form for creating a new forwarder.
     */
    public function create()
    {
        return Inertia::render('forwarder/create');
    }

    /**
     * Store a newly created forwarder.
     */
    public function store(ForwarderRequest $request)
    {
        Forwarders::create($request->validated());

        return redirect()
            ->route('forwarders.index')
            ->with('success', 'Forwarder created successfully.');
    }

    /**
     * Display the specified forwarder.
     */
    public function show(Forwarders $forwarder)
    {
        return Inertia::render('forwarder/show', [
            'forwarder' => $forwarder,
        ]);

    }

    /**
     * Show the form for editing the specified forwarder.
     */
    public function edit(Forwarders $forwarder)
    {
        return Inertia::render('forwarder/edit', [
            'forwarder' => $forwarder,
        ]);
    }

    /**
     * Update the specified forwarder.
     */
    public function update(
        ForwarderRequest $request,
        Forwarders $forwarder
    ) {
        $forwarder->update($request->validated());

        return redirect()
            ->route('forwarders.index')
            ->with('success', 'Forwarder updated successfully.');
    }

    /**
     * Remove the specified forwarder.
     */
    public function destroy(Forwarders $forwarder)
    {
        $forwarder->delete();

        return redirect()
            ->route('forwarders.index')
            ->with('success', 'Forwarder deleted successfully.');
    }
}
