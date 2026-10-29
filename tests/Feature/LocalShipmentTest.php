<?php

use App\Models\LocalShipmentMilestone;
use App\Models\LocalShipments;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\User;
use App\Models\Warehouses;
use Inertia\Testing\AssertableInertia as Assert;

test('local shipments persist and display milestones in the shared milestone shape', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-LOCAL-SHIP-001',
        'category' => 'LOCAL',
        'po_date' => '2026-10-14',
        'status' => '1',
    ]);
    $warehouse = Warehouses::query()->create([
        'warehouse_code' => 'WH-LOCAL-SHIP-001',
        'warehouse_name' => 'Local Warehouse',
    ]);

    $this->actingAs($user)
        ->post(route('local-shipment.store'), [
            'shipment_number' => 'LOCSHP-20261026-0001',
            'purchase_orders_id' => $purchaseOrder->id,
            'warehouse_id' => $warehouse->id,
            'delivery_order_number' => 'DO-LOCAL-001',
            'etd' => '2026-10-20',
            'atd' => '2026-10-21',
            'status_document' => 'Documents ready',
            'tracking_status' => 'in transit',
            'remarks' => 'Local shipment note',
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
    $milestone = LocalShipmentMilestone::query()->whereBelongsTo($shipment, 'localShipment')->firstOrFail();

    expect($milestone->planned_date->format('Y-m-d'))->toBe('2026-10-20')
        ->and($milestone->actual_date->format('Y-m-d'))->toBe('2026-10-21');

    $this->actingAs($user)
        ->get(route('local-shipment.edit', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('local-shipment/edit')
            ->where('localShipment.shipment_number', $shipment->shipment_number)
            ->where('milestones.0.milestone_name', 'Departed from warehouse')
            ->where('milestones.0.planned_date', '2026-10-20')
            ->where('milestones.0.actual_date', '2026-10-21'));

    $this->actingAs($user)
        ->get(route('local-shipment.show', $shipment))
        ->assertInertia(fn (Assert $page) => $page
            ->component('local-shipment/show')
            ->where('localShipment.milestones.0.status', 'Completed'));
});

test('updating a local shipment synchronizes its milestones', function () {
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
        'purchase_orders_id' => $purchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);
    $shipment->milestones()->create([
        'milestone_name' => 'Old milestone',
        'status' => 'Planned',
    ]);

    $this->actingAs($user)
        ->put(route('local-shipment.update', $shipment), [
            'shipment_number' => $shipment->shipment_number,
            'purchase_orders_id' => $purchaseOrder->id,
            'warehouse_id' => $warehouse->id,
            'delivery_order_number' => null,
            'etd' => null,
            'atd' => null,
            'status_document' => 'Documents ready',
            'tracking_status' => 'delivered',
            'remarks' => null,
            'milestones' => [[
                'milestone_name' => 'Delivered to destination',
                'status' => 'Completed',
                'planned_date' => '2026-10-24',
                'actual_date' => '2026-10-25',
                'remarks' => null,
            ]],
        ])
        ->assertRedirect(route('local-shipment.index'));

    expect($shipment->fresh()->milestones)->toHaveCount(1)
        ->and($shipment->fresh()->milestones->first()->milestone_name)->toBe('Delivered to destination');
});

test('local tracking shows local shipments and their milestones only', function () {
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
        'purchase_orders_id' => $localPurchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);
    $localShipment->milestones()->create([
        'milestone_name' => 'Out for delivery',
        'status' => 'In Progress',
        'planned_date' => '2026-10-26',
    ]);
    LocalShipments::query()->create([
        'shipment_number' => 'LOCSHP-TRACK-002',
        'purchase_orders_id' => $importPurchaseOrder->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $this->get(route('tracking.local'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('tracking/local')
            ->has('localShipments', 1)
            ->where('localShipments.0.purchase_order.po_number', 'PO-LOCAL-TRACK-001')
            ->where('localShipments.0.milestones.0.milestone_name', 'Out for delivery'));
});
