<?php

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\ShippingLineRequest;
use App\Http\Requests\SupplierRequest;
use App\Models\Forwarders;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('registers the core CRUD routes for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('company.index'))->assertOk();
    $this->get(route('suppliers.index'))->assertOk();
    $this->get(route('shipments.index'))->assertOk();
    $this->get(route('warehouses.index'))->assertOk();
});

it('passes the selected forwarder to the edit page', function () {
    $user = User::factory()->create();
    $forwarder = Forwarders::create([
        'forwarder_name' => 'Test Forwarder',
        'contact_person' => 'Test Contact',
        'email' => 'forwarder@example.com',
        'phone' => '08123456789',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('forwarders.edit', $forwarder))
        ->assertInertia(fn (Assert $page) => $page
            ->component('forwarder/edit')
            ->where('forwarder.id', $forwarder->id)
            ->where('forwarder.forwarder_name', 'Test Forwarder'));
});

it('uses valid request authorization and Laravel validation rules', function () {
    expect((new SupplierRequest)->authorize())->toBeTrue();
    expect((new CompanyRequest)->authorize())->toBeTrue();
    expect((new ShippingLineRequest)->authorize())->toBeTrue();

    $supplierRules = (new SupplierRequest)->rules();
    $shippingRules = (new ShippingLineRequest)->rules();

    expect($supplierRules['supplier_name'])->toContain('string');
    expect($supplierRules['email'])->toContain('nullable');
    expect($shippingRules['company_id'])->toContain('exists:companies,id');
    expect($shippingRules['shipping_line_name'])->toContain('string');
});
