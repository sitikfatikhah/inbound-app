<?php

use App\Models\ShippingLines;
use App\Models\User;

test('shipping line updates persist validated form fields', function () {
    $user = User::factory()->create();
    $shippingLine = ShippingLines::query()->create([
        'shipping_line_name' => 'Original Shipping Line',
        'contact_person' => 'Original Contact',
        'email' => 'original@example.com',
        'phone' => '0800000000',
        'website' => 'https://original.example.com',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->put(route('shipping_line.update', $shippingLine), [
            'shipping_line_name' => 'Updated Shipping Line',
            'contact_person' => 'Updated Contact',
            'email' => 'updated@example.com',
            'phone' => '0812345678',
            'website' => 'https://updated.example.com',
            'is_active' => false,
        ])
        ->assertRedirect(route('shipping_line.index'))
        ->assertSessionDoesntHaveErrors();

    $updatedShippingLine = $shippingLine->fresh();

    expect($updatedShippingLine->shipping_line_name)->toBe('Updated Shipping Line')
        ->and($updatedShippingLine->contact_person)->toBe('Updated Contact')
        ->and($updatedShippingLine->email)->toBe('updated@example.com')
        ->and($updatedShippingLine->phone)->toBe('0812345678')
        ->and($updatedShippingLine->website)->toBe('https://updated.example.com')
        ->and((bool) $updatedShippingLine->is_active)->toBeFalse();
});
