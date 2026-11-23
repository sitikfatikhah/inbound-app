<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentScheduleRevisionRequest;
use App\Models\Shipments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ShipmentScheduleHistoriesController extends Controller
{
    public function store(
        StoreShipmentScheduleRevisionRequest $request,
        Shipments $shipment,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $shipment, $validated): void {
            foreach (['eta', 'etd'] as $scheduleType) {
                if (! $request->filled($scheduleType)) {
                    continue;
                }

                $previousDate = $shipment->{$scheduleType}?->toDateString();
                $newDate = $validated[$scheduleType];

                if ($previousDate === $newDate) {
                    continue;
                }

                $shipment->scheduleHistories()->create([
                    'schedule_type' => $scheduleType,
                    'previous_date' => $previousDate,
                    'new_date' => $newDate,
                    'revision_reason' => $validated['revision_reason'],
                    'source' => 'manual',
                    'updated_by' => $request->user()->id,
                ]);

                $shipment->{$scheduleType} = $newDate;
            }

            $shipment->save();
        });

        return back()->with('success', 'Shipment schedule updated and revision history recorded.');
    }
}
