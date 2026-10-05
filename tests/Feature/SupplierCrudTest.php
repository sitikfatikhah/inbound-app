<?php

use App\Models\Suppliers;
use App\Models\User;

it('can create a supplier via POST request', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'supplier_code' => 'SUP001',
        'supplier_name' => 'Test Supplier',
        'country' => 'Indonesia',
        'address' => 'Jl. Test No. 1',
        'contact_person' => 'John Doe',
        'email' => 'john@supplier.com',
        'phone' => '08123456789',
        'is_active' => true,
    ];

    $response = $this->post(route('suppliers.store'), $data);

    $response->assertRedirect(route('suppliers.index'));
    expect(Suppliers::where('supplier_code', 'SUP001')->exists())->toBeTrue();
});

it('validates supplier creation with required fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('suppliers.store'), [
        'supplier_code' => '',
        'supplier_name' => '',
    ]);

    $response->assertSessionHasErrors(['supplier_code', 'supplier_name']);
});

it('can update a supplier', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $supplier = Suppliers::factory()->create();

    $response = $this->put(route('suppliers.update', $supplier->id), [
        'supplier_code' => $supplier->supplier_code,
        'supplier_name' => 'New Supplier Name',
        'country' => 'Indonesia',
        'address' => 'Updated address',
        'contact_person' => 'Jane Doe',
        'email' => 'jane@supplier.com',
        'phone' => '08987654321',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('suppliers.index'));
    $supplier->refresh();
    expect($supplier->supplier_name)->toBe('New Supplier Name');
});
