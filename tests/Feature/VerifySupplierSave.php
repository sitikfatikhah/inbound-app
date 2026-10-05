<?php

use App\Models\Suppliers;
use App\Models\User;

it('verifies that supplier data is actually saved to database', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Supplier count before
    $countBefore = Suppliers::count();
    expect($countBefore)->toBe(0);

    // Create supplier via POST request
    $data = [
        'supplier_code' => 'VERIFY-001',
        'supplier_name' => 'Verification Supplier',
        'country' => 'Indonesia',
        'address' => 'Test Address',
        'contact_person' => 'Test Person',
        'email' => 'test@supplier.com',
        'phone' => '081234567890',
        'is_active' => true,
    ];

    $response = $this->post(route('suppliers.store'), $data);

    // Check redirect response
    $response->assertRedirect(route('suppliers.index'));

    // Supplier count after
    $countAfter = Suppliers::count();
    expect($countAfter)->toBe(1);

    // Verify supplier exists in database
    $supplier = Suppliers::where('supplier_code', 'VERIFY-001')->first();
    expect($supplier)->not->toBeNull();
    expect($supplier->supplier_name)->toBe('Verification Supplier');
    expect($supplier->country)->toBe('Indonesia');
    expect($supplier->email)->toBe('test@supplier.com');
    expect($supplier->contact_person)->toBe('Test Person');
    expect($supplier->phone)->toBe('081234567890');
    expect($supplier->is_active)->toBeTruthy();
});
