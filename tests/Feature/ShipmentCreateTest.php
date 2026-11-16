<?php

use App\Models\Forwarders;
use App\Models\LocalShipments;
use App\Models\Products;
use App\Models\PurchaseOrderItems;
use App\Models\PurchaseOrders;
use App\Models\ShipmentMilestones;
use App\Models\Shipments;
use App\Models\Suppliers;
use App\Models\User;
use App\Models\Warehouses;
use Inertia\Testing\AssertableInertia as Assert;

test('shipment create page includes purchase order lines with remaining quantities', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $product = Products::query()->create([
        'product_code' => 'PROD-SHIP-001',
        'product_name' => 'Shipment test product',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-SHIP-001',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 12,
        'backorder_qty' => 12,
        'remaining_qty' => 8,
        'total_allocated' => 4,
        'unit' => 'PCS',
    ]);

    $this->actingAs($user)
        ->get(route('shipments.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('shipment/create')
            ->has('purchaseOrderItems', 1)
            ->where('purchaseOrderItems.0.product_code', 'PROD-SHIP-001')
            ->where('purchaseOrderItems.0.remaining_qty', 8));
});

test('purchase orders can be split across shipments and each shipment can contain multiple purchase orders', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $forwarder = Forwarders::query()->create(['forwarder_name' => 'Test Forwarder']);
    $purchaseOrderOne = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-SHIP-101',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $purchaseOrderTwo = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-SHIP-102',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $createItem = function (PurchaseOrders $purchaseOrder, string $productCode, int $quantity): PurchaseOrderItems {
        $product = Products::query()->create([
            'product_code' => $productCode,
            'product_name' => "Product $productCode",
            'unit' => 'PCS',
            'is_active' => true,
        ]);

        return $purchaseOrder->items()->create([
            'product_id' => $product->id,
            'product_code' => $productCode,
            'product_name' => $product->product_name,
            'quantity' => $quantity,
            'backorder_qty' => $quantity,
            'remaining_qty' => $quantity,
            'total_allocated' => 0,
            'unit' => 'PCS',
        ]);
    };
    $itemOne = $createItem($purchaseOrderOne, 'PROD-SHIP-101', 10);
    $itemTwo = $createItem($purchaseOrderTwo, 'PROD-SHIP-102', 5);

    $this->actingAs($user)
        ->post(route('shipments.store'), [
            'shipment_number' => 'SHP-20261015-0001',
            'forwarder_id' => $forwarder->id,
            'status_document' => 'purchase order created',
            'tracking_status' => 'not shipped',
            'purchase_order_ids' => [$purchaseOrderOne->id, $purchaseOrderTwo->id],
            'containers' => [[
                'container_number' => 'CONT-001',
                'container_seal_number' => 'SEAL-001',
                'items' => [
                    [
                        'purchase_order_id' => $purchaseOrderOne->id,
                        'purchase_order_item_id' => $itemOne->id,
                        'quantity' => 4,
                    ],
                    [
                        'purchase_order_id' => $purchaseOrderTwo->id,
                        'purchase_order_item_id' => $itemTwo->id,
                        'quantity' => 2,
                    ],
                ],
            ]],
        ])
        ->assertRedirect(route('shipments.index'))
        ->assertSessionDoesntHaveErrors();

    $this->actingAs($user)
        ->post(route('shipments.store'), [
            'shipment_number' => 'SHP-20261015-0002',
            'forwarder_id' => $forwarder->id,
            'status_document' => 'purchase order created',
            'tracking_status' => 'not shipped',
            'purchase_order_ids' => [$purchaseOrderOne->id],
            'containers' => [[
                'container_number' => 'CONT-002',
                'container_seal_number' => 'SEAL-002',
                'items' => [[
                    'purchase_order_id' => $purchaseOrderOne->id,
                    'purchase_order_item_id' => $itemOne->id,
                    'quantity' => 3,
                ]],
            ]],
        ])
        ->assertRedirect(route('shipments.index'))
        ->assertSessionDoesntHaveErrors();

    $shipments = $purchaseOrderOne->fresh()->shipments;
    $firstShipment = $shipments->firstWhere('shipment_number', 'SHP-20261015-0001');
    $secondShipment = Shipments::query()
        ->where('shipment_number', 'SHP-20261015-0002')
        ->firstOrFail();

    expect($shipments)->toHaveCount(2)
        ->and($firstShipment?->purchaseOrders)->toHaveCount(2)
        ->and((float) $itemOne->fresh()->remaining_qty)->toBe(3.0)
        ->and((float) $itemOne->fresh()->total_allocated)->toBe(7.0)
        ->and((float) $itemTwo->fresh()->remaining_qty)->toBe(3.0)
        ->and((float) $itemTwo->fresh()->total_allocated)->toBe(2.0);

    $this->actingAs($user)
        ->get(route('shipments.edit', $firstShipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('shipment/edit')
            ->has('containers', 1)
            ->has('containers.0.items', 2));

    $this->actingAs($user)
        ->put(route('shipments.update', $secondShipment), [
            'shipment_number' => $secondShipment->shipment_number,
            'forwarder_id' => $forwarder->id,
            'status_document' => 'purchase order created',
            'tracking_status' => 'not shipped',
            'containers' => [[
                'container_number' => 'CONT-002',
                'container_seal_number' => 'SEAL-002',
                'items' => [[
                    'purchase_order_id' => $purchaseOrderOne->id,
                    'purchase_order_item_id' => $itemOne->id,
                    'quantity' => 2,
                ]],
            ]],
        ])
        ->assertRedirect(route('shipments.index'))
        ->assertSessionDoesntHaveErrors();

    expect((float) $itemOne->fresh()->remaining_qty)->toBe(4.0)
        ->and((float) $itemOne->fresh()->total_allocated)->toBe(6.0);

    $this->actingAs($user)
        ->post(route('shipments.store'), [
            'shipment_number' => 'SHP-20261015-0003',
            'forwarder_id' => $forwarder->id,
            'status_document' => 'purchase order created',
            'tracking_status' => 'not shipped',
            'containers' => [[
                'items' => [[
                    'purchase_order_id' => $purchaseOrderOne->id,
                    'purchase_order_item_id' => $itemOne->id,
                    'quantity' => 5,
                ]],
            ]],
        ])
        ->assertSessionHasErrors('containers.0.items.0.quantity');

    $this->actingAs($user)
        ->delete(route('shipments.destroy', $secondShipment))
        ->assertRedirect(route('shipments.index'));

    expect($purchaseOrderOne->fresh()->shipments)->toHaveCount(1)
        ->and((float) $itemOne->fresh()->remaining_qty)->toBe(6.0)
        ->and((float) $itemOne->fresh()->total_allocated)->toBe(4.0);
});

test('arrival schedule includes invoice purchase orders and product categories', function () {
    $supplier = Suppliers::factory()->create();
    $product = Products::query()->create([
        'product_code' => 'PROD-SCHEDULE-001',
        'product_name' => 'Schedule test product',
        'category' => 'Electronics',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-SCHEDULE-001',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 12,
        'unit' => 'PCS',
    ]);
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-SCHEDULE-001',
        'invoice_number' => 'INV-SCHEDULE-001',
        'eta' => '2026-10-20',
    ]);
    $purchaseOrder->shipments()->attach($shipment);

    $this->get(route('arrival-schedule'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/schedule')
            ->where('shipments.0.invoice_number', 'INV-SCHEDULE-001')
            ->where('shipments.0.purchase_orders.0.po_number', 'PO-SCHEDULE-001')
            ->where('shipments.0.purchase_orders.0.items.0.product.category', 'Electronics'));
});

test('shipment milestones are stored and displayed on import tracking', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $forwarder = Forwarders::query()->create(['forwarder_name' => 'Milestone Forwarder']);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-MILESTONE-001',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $product = Products::query()->create([
        'product_code' => 'PROD-MILESTONE-001',
        'product_name' => 'Milestone test product',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $item = $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 12,
        'backorder_qty' => 12,
        'remaining_qty' => 12,
        'unit' => 'PCS',
    ]);

    $this->actingAs($user)
        ->post(route('shipments.store'), [
            'shipment_number' => 'SHP-MILESTONE-001',
            'forwarder_id' => $forwarder->id,
            'status_document' => 'PL & INV created',
            'tracking_status' => 'vessel departed',
            'etd' => '2026-10-19',
            'eta' => '2026-10-25',
            'purchase_order_ids' => [$purchaseOrder->id],
            'milestones' => [[
                'milestone_name' => 'Vessel departed',
                'status' => 'Completed',
                'planned_date' => '2026-10-19',
                'actual_date' => '2026-10-19',
                'remarks' => 'Departed on schedule',
            ]],
            'containers' => [[
                'container_number' => 'CONT-MILESTONE-001',
                'container_seal_number' => 'SEAL-MILESTONE-001',
                'items' => [[
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_order_item_id' => $item->id,
                    'quantity' => 4,
                ]],
            ]],
        ])
        ->assertRedirect(route('shipments.index'));

    $shipment = Shipments::query()->where('shipment_number', 'SHP-MILESTONE-001')->firstOrFail();

    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => 'Vessel departed',
        'status' => 'Completed',
    ]);
    $milestone = ShipmentMilestones::query()->where('shipment_id', $shipment->id)->firstOrFail();

    expect($milestone->planned_date->format('Y-m-d'))->toBe('2026-10-19')
        ->and($milestone->actual_date->format('Y-m-d'))->toBe('2026-10-19');
    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => '[System] Purchase Order: PO-MILESTONE-001',
        'actual_date' => '2026-10-14 00:00:00',
    ]);
    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => '[System] Estimated Departure (ETD)',
        'planned_date' => '2026-10-19 00:00:00',
    ]);
    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => '[System] Document status: PL & INV created',
    ]);
    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => '[System] Tracking status: vessel departed',
    ]);
    $this->assertDatabaseHas('shipment_status_histories', [
        'shipment_id' => $shipment->id,
        'status' => 'Tracking status: vessel departed',
    ]);
    $documentMilestone = ShipmentMilestones::query()
        ->where('shipment_id', $shipment->id)
        ->where('milestone_name', '[System] Document status: PL & INV created')
        ->firstOrFail();
    $trackingMilestone = ShipmentMilestones::query()
        ->where('shipment_id', $shipment->id)
        ->where('milestone_name', '[System] Tracking status: vessel departed')
        ->firstOrFail();

    expect($documentMilestone->actual_date)->not->toBeNull()
        ->and($trackingMilestone->actual_date)->not->toBeNull();

    $this->get('/tracking/import')
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/import')
            ->where('shipments.0.etd', '2026-10-19T00:00:00.000000Z')
            ->where('shipments.0.eta', '2026-10-25T00:00:00.000000Z')
            ->has('shipments.0.milestones', 6));

    $this->actingAs($user)
        ->get(route('shipments.edit', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('shipment/edit')
            ->has('milestones', 1));

    $this->actingAs($user)
        ->put(route('shipments.update', $shipment), [
            'shipment_number' => $shipment->shipment_number,
            'status_document' => 'BL Final',
            'tracking_status' => 'in transit',
        ])
        ->assertRedirect(route('shipments.index'));

    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => '[System] Document status: BL Final',
        'status' => 'Completed',
    ]);
    $this->assertDatabaseHas('shipment_milestones', [
        'shipment_id' => $shipment->id,
        'milestone_name' => '[System] Tracking status: in transit',
        'status' => 'In Progress',
    ]);
    $this->assertDatabaseHas('shipment_status_histories', [
        'shipment_id' => $shipment->id,
        'status' => 'Document status: BL Final',
    ]);
    $this->assertDatabaseHas('shipment_status_histories', [
        'shipment_id' => $shipment->id,
        'status' => 'Tracking status: in transit',
    ]);
});

test('local tracking only includes shipments linked to local purchase orders', function () {
    $supplier = Suppliers::factory()->create();
    $localPurchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-001',
        'category' => 'LOCAL',
        'po_date' => '2026-10-14',
    ]);
    $importPurchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-IMPORT-001',
        'category' => 'IMPORT',
        'po_date' => '2026-10-14',
    ]);
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-TRACK-LOCAL-001',
        'warehouse_name' => 'Local Warehouse',
    ]);
    LocalShipments::query()->create([
        'shipment_number' => 'LOCSHP-TRACK-LOCAL-001',
        'purchase_orders_id' => $localPurchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);
    LocalShipments::query()->create([
        'shipment_number' => 'LOCSHP-TRACK-IMPORT-001',
        'purchase_orders_id' => $importPurchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $this->get(route('tracking.local'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/local')
            ->has('localShipments', 1)
            ->where('localShipments.0.purchase_order.po_number', 'PO-LOCAL-001'));
});

test('arrival warehouse route returns receipt data', function () {
    $this->get(route('warehouse'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/warehouse')
            ->has('receipts', 0));
});

test('analytics receives PO arrival dates and shipment actual arrival dates', function () {
    $supplier = Suppliers::factory()->create();
    PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-ANALYTICS-001',
        'po_date' => '2026-10-14',
        'arrival_date' => '2026-11-03',
    ]);
    Shipments::query()->create([
        'shipment_number' => 'SHP-ANALYTICS-001',
        'eta' => '2026-11-01',
        'ata' => '2026-11-06',
    ]);

    $this->get(route('analytics'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/analytics')
            ->where('purchaseOrders.0.arrival_date', '2026-11-03T00:00:00.000000Z')
            ->where('shipments.0.ata', '2026-11-06T00:00:00.000000Z'));
});

test('project monitoring only includes shipment items with sample products', function () {
    $supplier = Suppliers::factory()->create();
    $sampleProduct = Products::query()->create([
        'product_code' => 'PROD-SAMPLE-001',
        'product_name' => 'Sample product',
        'category' => 'Sample',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $regularProduct = Products::query()->create([
        'product_code' => 'PROD-REGULAR-001',
        'product_name' => 'Regular product',
        'category' => 'Electronics',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-PROJECT-001',
        'po_date' => '2026-10-14',
    ]);
    $sampleItem = $purchaseOrder->items()->create([
        'product_id' => $sampleProduct->id,
        'product_code' => $sampleProduct->product_code,
        'product_name' => $sampleProduct->product_name,
        'quantity' => 10,
        'unit' => 'PCS',
    ]);
    $regularItem = $purchaseOrder->items()->create([
        'product_id' => $regularProduct->id,
        'product_code' => $regularProduct->product_code,
        'product_name' => $regularProduct->product_name,
        'quantity' => 20,
        'unit' => 'PCS',
    ]);
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-PROJECT-001',
        'eta' => '2026-11-01',
    ]);
    $shipment->shipmentContainers()->createMany([
        [
            'purchase_order_item_id' => $sampleItem->id,
            'product_code' => $sampleProduct->product_code,
            'quantity' => 4,
        ],
        [
            'purchase_order_item_id' => $regularItem->id,
            'product_code' => $regularProduct->product_code,
            'quantity' => 8,
        ],
    ]);

    $this->get(route('project-monitoring'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/project')
            ->has('projects', 1)
            ->where('projects.0.product.category', 'Sample')
            ->where('projects.0.quantity', 4)
            ->where('projects.0.shipment.shipment_number', 'SHP-PROJECT-001'));
});

test('shipment show includes allocated purchase order item details and status history', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $product = Products::query()->create([
        'product_code' => 'PROD-SHIP-SHOW-001',
        'product_name' => 'Shipment detail item',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-SHIP-SHOW-001',
        'category' => 'IMPORT',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $purchaseOrderItem = $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 10,
        'backorder_qty' => 10,
        'remaining_qty' => 6,
        'total_allocated' => 4,
        'unit' => 'PCS',
    ]);
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-SHOW-001',
    ]);
    $shipment->shipmentContainers()->create([
        'purchase_order_item_id' => $purchaseOrderItem->id,
        'product_code' => $product->product_code,
        'quantity' => 4,
        'container_number' => 'CONT-SHOW-001',
    ]);
    $shipment->statusHistories()->create([
        'status' => 'Tracking status: in transit',
        'status_date' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('shipments.show', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('shipment/show')
            ->where('shipment.shipment_containers.0.purchase_order_item.product_name', 'Shipment detail item')
            ->where('shipment.shipment_containers.0.purchase_order_item.unit', 'PCS')
            ->where('shipment.shipment_containers.0.container_number', 'CONT-SHOW-001')
            ->where('shipment.status_histories.0.status', 'Tracking status: in transit'));
});
