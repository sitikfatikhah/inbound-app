<?php

use App\Models\TrackingStatus;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('uses the tracking statuses table for the index', function () {
    $status = TrackingStatus::query()->create([
        'category' => 'shipment',
        'code' => 'in_transit',
        'name' => 'In Transit',
        'is_active' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('tracking-status.index'))
        ->assertSuccessful();

    expect($status->getTable())->toBe('tracking_statuses')
        ->and(TrackingStatus::query()->findOrFail($status->id)->code)->toBe('in_transit');
});

it('shows the saved tracking status in the edit form', function () {
    $status = TrackingStatus::query()->create([
        'category' => 'shipment',
        'code' => 'in_transit',
        'name' => 'In Transit',
        'is_active' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('tracking-status.edit', $status))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking-status/edit')
            ->where('tracking_statuses.id', $status->id)
            ->where('tracking_statuses.category', 'shipment')
            ->where('tracking_statuses.code', 'in_transit')
            ->where('tracking_statuses.name', 'In Transit')
            ->where('tracking_statuses.is_active', true));
});

it('updates a tracking status without treating its existing code as a duplicate', function () {
    $status = TrackingStatus::query()->create([
        'category' => 'shipment',
        'code' => 'in_transit',
        'name' => 'In Transit',
        'is_active' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->put(route('tracking-status.update', $status), [
            'category' => 'shipment',
            'code' => 'in_transit',
            'name' => 'In Transit Updated',
            'is_active' => false,
        ])
        ->assertRedirect(route('tracking-status.index'))
        ->assertSessionDoesntHaveErrors();

    expect($status->fresh()->name)->toBe('In Transit Updated')
        ->and($status->fresh()->is_active)->toBeFalse();
});
