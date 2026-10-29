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
        $status = TrackingStatus::latest()->get();

        return Inertia::render('tracking-status/index', [
            'status' => $status,
        ]);
    }

    /**
     * Show the form for creating a new tracking status.
     */
    public function create(): Response
    {
        return Inertia::render('tracking-status/create');
    }

    /**
     * Store a newly created tracking status.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:30'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tracking_status', 'code')->where(fn ($query) => $query->where('category', $request->string('category'))),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        TrackingStatus::create($validated);

        return redirect()
            ->route('tracking-status.index')
            ->with('success', 'Status created successfully.');
    }

    /**
     * Display the specified tracking status.
     */
    public function show(TrackingStatus $status): Response
    {
        return Inertia::render('tracking-status/show', [
            'status' => $status,
        ]);
    }

    /**
     * Show the form for editing the specified tracking status.
     */
    public function edit(TrackingStatus $status): Response
    {
        return Inertia::render('tracking-status/edit', [
            'status' => $status,
        ]);
    }

    /**
     * Update the specified tracking status.
     */
    public function update(
        Request $request,
        TrackingStatus $status
    ): RedirectResponse {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:30'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tracking_status', 'code')
                    ->where(fn ($query) => $query->where('category', $request->string('category')))
                    ->ignore($status->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $status->update($validated);

        return redirect()
            ->route('tracking-status.index')
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified tracking status.
     */
    public function destroy(TrackingStatus $status): RedirectResponse
    {
        $status->delete();

        return redirect()
            ->route('tracking-status.index')
            ->with('success', 'Status deleted successfully.');
    }
}
