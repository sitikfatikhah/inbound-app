<?php

use App\Models\LocalShipments;
use App\Models\Products;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\TrackingStatus;
use App\Models\User;
use App\Models\Warehouses;
use Inertia\Testing\AssertableInertia as Assert;

test('local shipments create an ordered status history instead of manual milestones', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-SHIP-001',
        'category' => 'LOCAL',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $product = Products::query()->create([
        'product_code' => 'PROD-LOCAL-SHIP-001',
        'product_name' => 'Local shipment item',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrderItem = $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 10,
        'backorder_qty' => 10,
        'remaining_qty' => 10,
        'total_allocated' => 0,
        'unit' => 'PCS',
    ]);
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-LOCAL-SHIP-001',
        'warehouse_name' => 'Local Warehouse',
    ]);
    $trackingStatus = TrackingStatus::query()->create([
        'category' => 'shipment',
        'code' => 'in_transit',
        'name' => 'in transit',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('local-shipment.store'), [
            'shipment_number' => 'LOCSHP-20261026-0001',
            'purchase_order_id' => $purchaseOrder->id,
            'warehouse_id' => $warehouse->id,
            'delivery_order_number' => 'DO-LOCAL-001',
            'etd' => '2026-10-20',
            'atd' => '2026-10-21',
            'status_document' => 'Documents ready',
            'tracking_status' => 'in transit',
            'remarks' => 'Local shipment note',
            'items' => [[
                'purchase_order_id' => $purchaseOrder->id,
                'purchase_order_item_id' => $purchaseOrderItem->id,
                'quantity' => 2,
            ]],
            'milestones' => [[
                'milestone_name' => 'Departed from warehouse',
                'status' => 'Completed',
                'planned_date' => '2026-10-20',
                'actual_date' => '2026-10-21',
                'remarks' => 'Departed one day late',
            ]],
        ])
        ->assertRedirect(route('local-shipment.index'));

    $shipment = LocalShipments::query()->where('shipment_number', 'LOCSHP-20261026-0001')->firstOrFail();
    $statusHistories = $shipment->statusHistories;

    expect($statusHistories)->toHaveCount(2)
        ->and($statusHistories[0]->status_type)->toBe('document')
        ->and($statusHistories[0]->status)->toBe('Documents ready')
        ->and($statusHistories[1]->status_type)->toBe('shipment')
        ->and($statusHistories[1]->status)->toBe('in transit')
        ->and($shipment->milestones()->count())->toBe(0)
        ->and($shipment->purchaseOrder->is($purchaseOrder))->toBeTrue()
        ->and($shipment->purchaseOrders)->toHaveCount(1)
        ->and($shipment->warehouse->is($warehouse))->toBeTrue()
        ->and($shipment->trackingStatus->is($trackingStatus))->toBeTrue()
        ->and($shipment->items)->toHaveCount(1)
        ->and((float) $purchaseOrderItem->fresh()->remaining_qty)->toBe(8.0)
        ->and((float) $purchaseOrderItem->fresh()->total_allocated)->toBe(2.0);

    $this->actingAs($user)
        ->get(route('local-shipment.edit', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('local-shipment/edit')
            ->where('localShipment.shipment_number', $shipment->shipment_number)
            ->where('items.0.purchase_order_item_id', $purchaseOrderItem->id)
            ->where('purchaseOrderItems.0.id', $purchaseOrderItem->id));

    $this->actingAs($user)
        ->get(route('local-shipment.show', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('local-shipment/show')
            ->where('localShipment.status_histories.0.status_type', 'document')
            ->where('localShipment.status_histories.1.status_type', 'shipment')
            ->where('localShipment.items.0.quantity', 2)
            ->where('localShipment.items.0.purchase_order_item.product_code', $product->product_code)
            ->where('localShipment.items.0.purchase_order_item.product_name', $product->product_name)
            ->where('localShipment.items.0.purchase_order_item.unit', 'PCS'));

    $this->actingAs($user)
        ->put(route('local-shipment.update', $shipment), [
            'shipment_number' => $shipment->shipment_number,
            'purchase_order_id' => $purchaseOrder->id,
            'warehouse_id' => $warehouse->id,
            'delivery_order_number' => 'DO-LOCAL-001',
            'etd' => '2026-10-20',
            'atd' => '2026-10-21',
            'status_document' => 'Documents ready',
            'tracking_status' => 'in transit',
            'remarks' => 'Local shipment note',
            'items' => [[
                'purchase_order_id' => $purchaseOrder->id,
                'purchase_order_item_id' => $purchaseOrderItem->id,
                'quantity' => 4,
            ]],
        ])
        ->assertRedirect(route('local-shipment.index'))
        ->assertSessionDoesntHaveErrors();

    expect((float) $purchaseOrderItem->fresh()->remaining_qty)->toBe(6.0)
        ->and((float) $purchaseOrderItem->fresh()->total_allocated)->toBe(4.0)
        ->and((float) $shipment->fresh()->items->first()->quantity)->toBe(4.0);
});

test('local shipment can allocate items from multiple local purchase orders', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-LOCAL-MULTI',
        'warehouse_name' => 'Local Warehouse',
    ]);
    $purchaseOrders = collect(['101', '102'])->map(function (string $suffix) use ($supplier): array {
        $purchaseOrder = PurchaseOrders::query()->create([
            'supplier_id' => $supplier->id,
            'po_number' => "PO-LOCAL-MULTI-$suffix",
            'category' => 'local',
            'po_date' => '2026-10-14',
            'status' => '1',
        ]);
        $product = Products::query()->create([
            'product_code' => "PROD-LOCAL-MULTI-$suffix",
            'product_name' => "Local item $suffix",
            'unit' => 'PCS',
            'is_active' => true,
        ]);
        $item = $purchaseOrder->items()->create([
            'product_id' => $product->id,
            'product_code' => $product->product_code,
            'product_name' => $product->product_name,
            'quantity' => 10,
            'backorder_qty' => 10,
            'remaining_qty' => 10,
            'total_allocated' => 0,
            'unit' => 'PCS',
        ]);

        return [$purchaseOrder, $item];
    });

    $this->actingAs($user)
        ->post(route('local-shipment.store'), [
            'shipment_number' => 'LOCSHP-MULTI-001',
            'purchase_order_id' => $purchaseOrders[0][0]->id,
            'warehouse_id' => $warehouse->id,
            'status_document' => 'Documents ready',
            'tracking_status' => 'in transit',
            'items' => [
                [
                    'purchase_order_id' => $purchaseOrders[0][0]->id,
                    'purchase_order_item_id' => $purchaseOrders[0][1]->id,
                    'quantity' => 3,
                ],
                [
                    'purchase_order_id' => $purchaseOrders[1][0]->id,
                    'purchase_order_item_id' => $purchaseOrders[1][1]->id,
                    'quantity' => 4,
                ],
            ],
        ])
        ->assertRedirect(route('local-shipment.index'))
        ->assertSessionDoesntHaveErrors();

    $shipment = LocalShipments::query()->where('shipment_number', 'LOCSHP-MULTI-001')->firstOrFail();

    expect($shipment->purchaseOrders)->toHaveCount(2)
        ->and($shipment->items)->toHaveCount(2)
        ->and($purchaseOrders[0][0]->allocatedLocalShipments->contains($shipment))->toBeTrue()
        ->and($purchaseOrders[1][0]->allocatedLocalShipments->contains($shipment))->toBeTrue()
        ->and((float) $purchaseOrders[0][1]->fresh()->remaining_qty)->toBe(7.0)
        ->and((float) $purchaseOrders[1][1]->fresh()->remaining_qty)->toBe(6.0);
});

test('local shipment rejects allocations above the remaining purchase order quantity', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-OVERALLOC',
        'category' => 'local',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $product = Products::query()->create([
        'product_code' => 'PROD-LOCAL-OVERALLOC',
        'product_name' => 'Limited local item',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrderItem = $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 3,
        'backorder_qty' => 3,
        'remaining_qty' => 3,
        'total_allocated' => 0,
        'unit' => 'PCS',
    ]);
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-LOCAL-OVERALLOC',
        'warehouse_name' => 'Local Warehouse',
    ]);

    $this->actingAs($user)
        ->post(route('local-shipment.store'), [
            'shipment_number' => 'LOCSHP-OVERALLOC-001',
            'purchase_order_id' => $purchaseOrder->id,
            'warehouse_id' => $warehouse->id,
            'status_document' => 'Documents ready',
            'tracking_status' => 'in transit',
            'items' => [[
                'purchase_order_id' => $purchaseOrder->id,
                'purchase_order_item_id' => $purchaseOrderItem->id,
                'quantity' => 4,
            ]],
        ])
        ->assertSessionHasErrors('items.0.quantity');

    expect(LocalShipments::query()->where('shipment_number', 'LOCSHP-OVERALLOC-001')->exists())->toBeFalse()
        ->and((float) $purchaseOrderItem->fresh()->remaining_qty)->toBe(3.0)
        ->and((float) $purchaseOrderItem->fresh()->total_allocated)->toBe(0.0);
});

test('local shipment create page provides local purchase orders and active status options', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-SHIP-CREATE',
        'category' => 'local',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $product = Products::query()->create([
        'product_code' => 'PROD-LOCAL-SHIP-CREATE',
        'product_name' => 'Create page item',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 10,
        'backorder_qty' => 10,
        'remaining_qty' => 10,
        'total_allocated' => 0,
        'unit' => 'PCS',
    ]);
    TrackingStatus::query()->create([
        'category' => 'local-document',
        'code' => 'document-ready',
        'name' => 'Documents ready',
        'is_active' => true,
    ]);
    TrackingStatus::query()->create([
        'category' => 'local-shipment',
        'code' => 'in-transit',
        'name' => 'In transit',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('local-shipment.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('local-shipment/create')
            ->where('purchaseOrders.0.id', $purchaseOrder->id)
            ->where('purchaseOrderItems.0.purchase_order_id', $purchaseOrder->id)
            ->where('status_document.0.name', 'Documents ready')
            ->where('tracking_status.0.name', 'In transit'));
});

test('updating a local shipment records only changed statuses', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-SHIP-002',
        'category' => 'local',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-LOCAL-SHIP-002',
        'warehouse_name' => 'Local Warehouse',
    ]);
    $shipment = LocalShipments::query()->create([
        'shipment_number' => 'LOCSHP-20261026-0002',
        'purchase_order_id' => $purchaseOrder->id,
        'warehouse_id' => $warehouse->id,
        'status_document' => 'Documents ready',
        'tracking_status' => 'in transit',
    ]);
    $shipment->milestones()->create([
        'milestone_name' => 'Old milestone',
        'status' => 'Planned',
    ]);

    $updateData = [
        'shipment_number' => $shipment->shipment_number,
        'purchase_order_id' => $purchaseOrder->id,
        'warehouse_id' => $warehouse->id,
        'delivery_order_number' => null,
        'etd' => null,
        'atd' => null,
        'status_document' => 'Documents checked',
        'tracking_status' => 'delivered',
        'remarks' => null,
        'milestones' => [[
            'milestone_name' => 'Delivered to destination',
            'status' => 'Completed',
            'planned_date' => '2026-10-24',
            'actual_date' => '2026-10-25',
            'remarks' => null,
        ]],
    ];

    auth()->login($user);

    $this->put(route('local-shipment.update', $shipment), $updateData)
        ->assertRedirect(route('local-shipment.index'));

    $this->put(route('local-shipment.update', $shipment), [
        ...$updateData,
        'status_document' => 'Documents checked',
        'tracking_status' => 'out for delivery',
    ])
        ->assertRedirect(route('local-shipment.index'));

    $statusHistories = $shipment->fresh()->statusHistories;

    expect($shipment->fresh()->milestones)->toHaveCount(1)
        ->and($shipment->fresh()->milestones->first()->milestone_name)->toBe('Old milestone')
        ->and($statusHistories)->toHaveCount(3)
        ->and($statusHistories->pluck('status')->all())->toBe([
            'Documents checked',
            'delivered',
            'out for delivery',
        ]);
});

test('local tracking shows local shipments and their status history only', function () {
    $supplier = Suppliers::factory()->create();
    $localPurchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-TRACK-001',
        'category' => 'LOCAL',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $importPurchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-IMPORT-TRACK-001',
        'category' => 'IMPORT',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-LOCAL-TRACK-001',
        'warehouse_name' => 'Local Warehouse',
    ]);
    $localShipment = LocalShipments::query()->create([
        'shipment_number' => 'LOCSHP-TRACK-001',
        'purchase_order_id' => $localPurchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);
    $localShipment->statusHistories()->create([
        'status_type' => 'shipment',
        'status' => 'Out for delivery',
        'status_date' => now(),
    ]);
    LocalShipments::query()->create([
        'shipment_number' => 'LOCSHP-TRACK-002',
        'purchase_order_id' => $importPurchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $this->get(route('tracking.local'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/local')
            ->has('localShipments', 1)
            ->where('localShipments.0.purchase_order.po_number', 'PO-LOCAL-TRACK-001')
            ->where('localShipments.0.status_histories.0.status', 'Out for delivery'));
});
