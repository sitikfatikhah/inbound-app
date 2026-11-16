<?php

use App\Models\Products;
use App\Models\PurchaseOrderItems;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('stores a purchase order with a string status', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $product = Products::query()->create([
        'product_code' => 'PROD-TEST-001',
        'product_name' => 'Test product',
        'unit' => 'PCS',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post(route('po.store'), [
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-TEST-001',
        'category' => 'IMPORT',
        'po_date' => '2026-09-10',
        'currency' => 'USD',
        'exchange_rate' => '1.00',
        'incoterm' => 'FOB',
        'payment_term' => '30 days',
        'delivery_date' => '2026-09-15',
        'arrival_date' => '2026-09-20',
        'status' => 'DRAFT',
        'items' => [
            [
                'product_id' => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'quantity' => 1.5,
                'unit' => 'PCS',
                'unit_price' => 12.5,
                'total_price' => 18.75,
            ],
        ],
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

    $item = PurchaseOrderItems::query()->firstOrFail();
    expect((float) $item->quantity)->toBe(1.5)
        ->and((float) $item->unit_price)->toBe(12.5)
        ->and((float) $item->total_price)->toBe(18.75)
        ->and((float) $item->backorder_qty)->toBe(1.5)
        ->and((float) $item->remaining_qty)->toBe(1.5)
        ->and((float) $item->total_allocated)->toBe(0.0);

    $this->actingAs($user)
        ->get(route('po.show', PurchaseOrders::query()->firstOrFail()))
        ->assertInertia(fn (Assert $page) => $page
            ->component('po/show')
            ->has('purchaseOrder.items', 1)
            ->where('purchaseOrder.items.0.product_code', 'PROD-TEST-001')
            ->where('purchaseOrder.items.0.unit_price', 12.5));
});

it('replaces purchase order items when updating', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $firstProduct = Products::query()->create([
        'product_code' => 'PROD-TEST-002',
        'product_name' => 'First product',
        'is_active' => true,
    ]);
    $secondProduct = Products::query()->create([
        'product_code' => 'PROD-TEST-003',
        'product_name' => 'Second product',
        'is_active' => true,
    ]);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-TEST-002',
        'category' => 'IMPORT',
        'po_date' => '2026-09-10',
        'status' => 'DRAFT',
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $firstProduct->id,
        'quantity' => 1,
        'unit_price' => 10,
        'total_price' => 10,
    ]);

    $response = $this->actingAs($user)->put(route('po.update', $purchaseOrder), [
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-TEST-002',
        'category' => 'IMPORT',
        'po_date' => '2026-09-10',
        'status' => 'DRAFT',
        'items' => [
            [
                'product_id' => $firstProduct->id,
                'quantity' => 2,
                'unit_price' => 10,
            ],
            [
                'product_id' => $secondProduct->id,
                'quantity' => 3,
                'unit_price' => 7.5,
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionDoesntHaveErrors();
    expect($purchaseOrder->items()->count())->toBe(2)
        ->and((float) $purchaseOrder->items()->where('product_id', $secondProduct->id)->firstOrFail()->total_price)->toBe(22.5);

    $this->actingAs($user)
        ->get(route('po.show', $purchaseOrder))
        ->assertInertia(fn (Assert $page) => $page
            ->component('po/show')
            ->has('purchaseOrder.items', 2));
});

it('requires a category when saving a purchase order', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('po.store'), [])
        ->assertRedirect()
        ->assertSessionHasErrors(['category', 'items']);

    expect(PurchaseOrders::query()->exists())->toBeFalse();
});
