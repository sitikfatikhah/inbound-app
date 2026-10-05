<?php

use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\User;

it('stores a purchase order with a string status', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();

    $response = $this->actingAs($user)->post(route('po.store'), [
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-TEST-001',
        'po_date' => '2026-09-10',
        'currency' => 'USD',
        'exchange_rate' => '1.00',
        'incoterm' => 'FOB',
        'payment_term' => '30 days',
        'delivery_date' => '2026-09-15',
        'arrival_date' => '2026-09-20',
        'status' => 'DRAFT',
    ]);

    $response->assertRedirect();
    $response->assertSessionDoesntHaveErrors();

    expect(PurchaseOrders::query()->first()->only([
        'supplier_id',
        'po_number',
        'status',
    ]))->toMatchArray([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-TEST-001',
        'status' => 'DRAFT',
    ]);
});
