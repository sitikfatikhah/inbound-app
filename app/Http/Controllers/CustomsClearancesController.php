<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCustomsClearanceRequest;
use App\Models\CustomsResponseType;
use App\Models\Shipments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CustomsClearancesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('customs-clearance/index', [
            'shipments' => Shipments::query()
                ->with(['pibClearance.responses'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(Shipments $shipment): Response
    {
        $shipment->load('pibClearance.responses.responseType');

        return Inertia::render('customs-clearance/show', [
            'shipment' => $shipment,
            'clearance' => $shipment->pibClearance,
            'responseTypes' => CustomsResponseType::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
        ]);
    }

    public function update(
        UpdateCustomsClearanceRequest $request,
        Shipments $shipment,
    ): RedirectResponse {
        DB::transaction(fn () => $shipment->pibClearance()->updateOrCreate(
            ['shipment_id' => $shipment->id],
            $request->validated(),
        ));

        return redirect()
            ->route('customs-clearance.show', $shipment)
            ->with('success', 'Customs clearance details saved.');
    }
}
