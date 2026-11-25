<?php

use App\Models\CustomsClearanceResponses;
use App\Models\CustomsResponseType;
use App\Models\Shipments;
use App\Models\User;
use Database\Seeders\CustomsResponseTypeSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('a shipment has one PIB clearance with multiple typed response records', function () {
    $user = User::factory()->create();
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-CUSTOMS-001',
    ]);
    $this->seed(CustomsResponseTypeSeeder::class);
    $responseTypes = CustomsResponseType::query()
        ->whereIn('code', ['SPJM', 'SPPB'])
        ->orderBy('id')
        ->get();

    $this->actingAs($user)
        ->put(route('customs-clearance.update', $shipment), [
            'pib_number' => 'PIB-001',
            'pib_date' => '2026-11-24',
            'customs_channel' => 'Red Line',
            'customs_status' => 'Inspection required',
        ])
        ->assertRedirect(route('customs-clearance.show', $shipment));

    $clearance = $shipment->pibClearance()->firstOrFail();

    $this->actingAs($user)
        ->put(route('customs-clearance.update', $shipment), [
            'pib_number' => 'PIB-UPDATED',
            'pib_date' => '2026-11-25',
            'customs_channel' => 'Green Line',
            'customs_status' => 'Released',
        ])
        ->assertRedirect(route('customs-clearance.show', $shipment));

    expect($shipment->pibClearance()->count())->toBe(1)
        ->and($clearance->refresh()->pib_number)->toBe('PIB-UPDATED');

    foreach ($responseTypes as $index => $responseType) {
        $this->actingAs($user)
            ->post(route('customs-clearance.responses.store', $clearance), [
                'customs_response_type_id' => $responseType->id,
                'document_number' => "{$responseType->code}-001",
                'response_date' => '2026-11-26',
                'action_required' => $index === 0 ? 'Submit supporting documents' : null,
                'action_taken' => $index === 0 ? 'Documents submitted' : null,
            ])
            ->assertRedirect(route('customs-clearance.show', $shipment));
    }

    expect(CustomsClearanceResponses::query()
        ->where('customs_clearance_id', $clearance->id)
        ->count())->toBe(2);

    $this->actingAs($user)
        ->get(route('customs-clearance.show', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('customs-clearance/show')
            ->where('clearance.pib_number', 'PIB-UPDATED')
            ->has('clearance.responses', 2)
            ->where('clearance.responses.0.response_type.code', 'SPPB'));

    $this->actingAs($user)
        ->get(route('customs-clearance-response.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('customs-clearance-response/index')
            ->has('responses.data', 2));
});
