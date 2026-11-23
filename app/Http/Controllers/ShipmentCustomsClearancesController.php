<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShipmentCustomsClearanceRequest;
use App\Models\Shipments;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ShipmentCustomsClearancesController extends Controller
{
    public function update(
        UpdateShipmentCustomsClearanceRequest $request,
        Shipments $shipment,
    ): RedirectResponse {
        $validated = $request->validated();
        $validated['inspection_duration'] = ($validated['inspection_start'] ?? null) && ($validated['inspection_end'] ?? null)
            ? (int) Carbon::parse($validated['inspection_start'])
                ->diffInMinutes(Carbon::parse($validated['inspection_end']))
            : null;
        $validated['created_by'] = $request->user()->id;

        DB::transaction(fn () => $shipment->customsClearance()->updateOrCreate(
            ['shipment_id' => $shipment->id],
            $validated,
        ));

        return back()->with('success', 'Customs clearance details updated.');
    }
}
