<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomsClearanceResponseRequest;
use App\Models\CustomsClearanceResponses;
use App\Models\CustomsClearances;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CustomsClearanceResponsesController extends Controller
{
    public function index(): Response
    {
        $responses = CustomsClearanceResponses::query()
            ->with(['customsClearance.shipment', 'responseType'])
            ->latest('response_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('customs-clearance-response/index', [
            'responses' => $responses,
        ]);
    }

    public function store(
        StoreCustomsClearanceResponseRequest $request,
        CustomsClearances $customsClearance,
    ): RedirectResponse {
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('customs-clearance-responses', 'public');
        }

        try {
            DB::transaction(function () use ($request, $customsClearance, $attachmentPath): void {
                $customsClearance->responses()->create([
                    ...$request->safe()->except('attachment'),
                    'attachment_path' => $attachmentPath,
                ]);
            });
        } catch (Throwable $e) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }

            throw $e;
        }

        return redirect()
            ->route('customs-clearance.show', $customsClearance->shipment_id)
            ->with('success', 'Customs response successfully recorded.');
    }

    public function show(CustomsClearanceResponses $response): Response
    {
        $response->load(['customsClearance.shipment', 'responseType']);
        $response->setAttribute(
            'attachment_url',
            $response->attachment_path
                ? Storage::disk('public')->url($response->attachment_path)
                : null,
        );

        return Inertia::render('customs-clearance-response/show', [
            'response' => $response,
        ]);
    }
}
