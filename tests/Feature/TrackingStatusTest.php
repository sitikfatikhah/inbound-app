<?php

use App\Models\TrackingStatus;
use App\Models\User;

test('tracking status index uses the tracking_status table', function () {
    $status = TrackingStatus::create([
        'category' => 'shipment',
        'code' => 'in_transit',
        'name' => 'In Transit',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('tracking-status.index'))
        ->assertOk();

    expect($status->getTable())->toBe('tracking_status')
        ->and(TrackingStatus::findOrFail($status->id)->code)->toBe('in_transit');
});
