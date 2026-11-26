<?php

namespace App\Http\Controllers;

use App\Models\TrackingStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TrackingStatusController extends Controller
{
    /**
     * Display a listing of tracking statuses.
     */
    public function index(): Response
    {
        $tracking_statuses = TrackingStatus::query()
            ->latest()
            ->get();

        return Inertia::render('tracking-status/index', [
            'tracking_statuses' => $tracking_statuses,
        ]);
    }

    /**
     * Show the form for creating a new tracking status.
     */
    public function create(): Response
    {
        return Inertia::render('tracking-status/create', [
            'tracking_statuses' => TrackingStatus::query()
                ->select('id', 'category')
                ->orderBy('category')
                ->get(),
        ]);
    }

    /**
     * Store a newly created tracking status.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => [
                'required',
                'string',
                'max:30',
            ],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tracking_statuses', 'code')
                    ->where('category', $request->input('category')),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        TrackingStatus::create($validated);

        return redirect()
            ->route('tracking-status.index')
            ->with('success', 'Status created successfully.');
    }

    /**
     * Display the specified tracking status.
     */
    public function show(TrackingStatus $tracking_status): Response
    {
        return Inertia::render('tracking-status/show', [
            'tracking_statuses' => $tracking_status,
        ]);
    }

    /**
     * Show the form for editing the specified tracking status.
     */
    public function edit(TrackingStatus $tracking_status): Response
    {
        return Inertia::render('tracking-status/edit', [
            'tracking_statuses' => $tracking_status,
        ]);
    }

    /**
     * Update the specified tracking status.
     */
    public function update(
        Request $request,
        TrackingStatus $tracking_status
    ): RedirectResponse {
        $validated = $request->validate([
            'category' => [
                'required',
                'string',
                'max:30',
            ],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tracking_statuses', 'code')
                    ->where('category', $request->input('category'))
                    ->ignore($tracking_status->id),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $tracking_status->update($validated);

        return redirect()
            ->route('tracking-status.index')
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified tracking status.
     */
    public function destroy(
        TrackingStatus $tracking_status
    ): RedirectResponse {
        $tracking_status->delete();

        return redirect()
            ->route('tracking-status.index')
            ->with('success', 'Status deleted successfully.');
    }
}
