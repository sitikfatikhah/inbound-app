<?php

use App\Models\Products;
use App\Models\PurchaseOrders;
use App\Models\Shipments;
use App\Models\Suppliers;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('shipment creation stores original dates and creates the required import document checklist', function () {
    $user = User::factory()->create();
    $supplier = Suppliers::factory()->create();
    $product = Products::query()->create([
        'product_code' => 'DOC-WORKFLOW-001',
        'product_name' => 'Workflow test product',
        'unit' => 'PCS',
        'is_active' => true,
    ]);
    $purchaseOrder = PurchaseOrders::query()->create([
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-DOC-WORKFLOW-001',
        'category' => 'IMPORT',
        'po_date' => '2026-11-01',
        'status' => '1',
    ]);
    $item = $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_code' => $product->product_code,
        'product_name' => $product->product_name,
        'quantity' => 5,
        'backorder_qty' => 5,
        'remaining_qty' => 5,
        'total_allocated' => 0,
        'unit' => 'PCS',
    ]);

    $this->actingAs($user)
        ->post(route('shipments.store'), [
            'shipment_number' => 'SHP-DOC-WORKFLOW-001',
            'eta' => '2026-12-10',
            'etd' => '2026-12-01',
            'containers' => [[
                'container_number' => 'CONT-DOC-001',
                'container_seal_number' => 'SEAL-DOC-001',
                'items' => [[
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_order_item_id' => $item->id,
                    'quantity' => 2,
                ]],
            ]],
        ])
        ->assertRedirect(route('shipments.index'));
    $shipment = Shipments::query()
        ->where('shipment_number', 'SHP-DOC-WORKFLOW-001')
        ->firstOrFail();

    expect($shipment->original_eta->toDateString())->toBe('2026-12-10')
        ->and($shipment->original_etd->toDateString())->toBe('2026-12-01')
        ->and($shipment->shipmentDocuments->pluck('document_type')->all())
        ->toBe(['invoice', 'packing_list', 'bill_of_lading', 'pib']);
});

test('schedule revisions require a reason and keep the original schedule unchanged', function () {
    $user = User::factory()->create();
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-SCHEDULE-WORKFLOW-001',
        'eta' => '2026-12-10',
        'etd' => '2026-12-01',
        'original_eta' => '2026-12-10',
        'original_etd' => '2026-12-01',
    ]);

    $this->actingAs($user)
        ->from(route('shipments.show', $shipment))
        ->post(route('shipments.schedule-revisions.store', $shipment), [
            'eta' => '2026-12-15',
            'revision_reason' => '',
        ])
        ->assertSessionHasErrors('revision_reason');

    expect($shipment->fresh()->eta->toDateString())->toBe('2026-12-10');

    $this->actingAs($user)
        ->from(route('shipments.show', $shipment))
        ->post(route('shipments.schedule-revisions.store', $shipment), [
            'eta' => '2026-12-15',
            'revision_reason' => 'Vessel departure delayed',
        ])
        ->assertRedirect(route('shipments.show', $shipment));

    $shipment->refresh();

    expect($shipment->eta->toDateString())->toBe('2026-12-15')
        ->and($shipment->original_eta->toDateString())->toBe('2026-12-10')
        ->and($shipment->scheduleHistories)->toHaveCount(1)
        ->and($shipment->scheduleHistories->first()->revision_reason)->toBe('Vessel departure delayed');
});

test('customs clearance records red lane inspections and raises SLA notifications', function () {
    $user = User::factory()->create();
    $this->travelTo(now()->setDate(2026, 11, 22)->setTime(12, 0));
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-CUSTOMS-WORKFLOW-001',
        'eta' => '2026-11-21',
    ]);

    $this->actingAs($user)
        ->put(route('shipments.customs-clearance.update', $shipment), [
            'jalur' => 'Merah',
            'pib_number' => 'PIB-WORKFLOW-001',
            'pib_date' => '2026-11-20',
            'inspection_start' => now()->subHours(25)->format('Y-m-d\TH:i'),
            'inspection_end' => '',
            'sppb_number' => '',
            'sppb_date' => '',
            'remarks' => '',
        ])
        ->assertRedirect();

    expect($shipment->fresh()->customsClearance->jalur)->toBe('Merah');

    $this->actingAs($user)
        ->get(route('shipments.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('notifications.0.type', 'critical')
            ->where('notifications.0.message', 'Shipment mendapat Jalur Merah.')
            ->where('notifications.1.type', 'warning')
            ->where('notifications.2.message', 'ETA terlewati tetapi barang belum diterima gudang.'));
});

test('shipment dashboard calculates document, inspection, ETA, and customs indicators', function () {
    $user = User::factory()->create();
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-INSIGHTS-WORKFLOW-001',
        'eta' => '2026-12-01',
        'ata' => '2026-11-22',
        'original_eta' => '2026-11-20',
    ]);

    foreach (config('documents.required_by_type.import') as $type) {
        $shipment->shipmentDocuments()->create([
            'document_type' => $type,
            'document_name' => config("documents.types.$type"),
            'document_file' => "shipment-documents/{$shipment->id}/{$type}.pdf",
            'verification_status' => 'complete',
        ]);
    }

    $shipment->customsClearance()->create([
        'jalur' => 'Merah',
        'pib_number' => 'PIB-INSIGHTS-001',
        'pib_date' => '2026-11-19',
        'sppb_number' => 'SPPB-INSIGHTS-001',
        'sppb_date' => '2026-11-21',
        'inspection_start' => '2026-11-20 08:00:00',
        'inspection_end' => '2026-11-20 11:00:00',
        'inspection_duration' => 180,
        'created_by' => $user->id,
    ]);

    foreach (range(1, 3) as $revisionNumber) {
        $shipment->scheduleHistories()->create([
            'schedule_type' => 'eta',
            'previous_date' => '2026-11-'.(19 + $revisionNumber),
            'new_date' => '2026-11-'.(20 + $revisionNumber),
            'revision_reason' => 'Vessel schedule change',
            'updated_by' => $user->id,
        ]);
    }

    $this->actingAs($user)
        ->get(route('shipments.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('insights.average_inspection_duration_hours', 3)
            ->where('insights.document_completeness_percentage', 100)
            ->where('insights.average_eta_revision_frequency', 3)
            ->where('insights.average_schedule_deviation_days', 2)
            ->where('insights.average_customs_clearance_lead_time_days', 2)
            ->where('insights.most_common_delay_reason.reason', 'Vessel schedule change')
            ->where('insights.most_common_delay_reason.count', 3)
            ->where('notifications.0.type', 'critical')
            ->where('notifications.1.type', 'warning')
            ->where('notifications.2.type', 'complete'));
});

test('shipment documents must be uploaded before verification', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-DOCUMENT-WORKFLOW-001',
    ]);
    $document = $shipment->shipmentDocuments()->create([
        'document_type' => 'invoice',
        'document_name' => 'Invoice',
        'verification_status' => 'missing',
    ]);

    $this->actingAs($user)
        ->from(route('shipments.show', $shipment))
        ->put(route('shipment-documents.update', $document), [
            'verification_status' => 'complete',
        ])
        ->assertSessionHasErrors('verification_status');

    $this->actingAs($user)
        ->from(route('shipments.show', $shipment))
        ->put(route('shipment-documents.update', $document), [
            'document_file' => UploadedFile::fake()->create('invoice.pdf', 20, 'application/pdf'),
        ])
        ->assertRedirect();

    $document->refresh();
    expect($document->verification_status)->toBe('pending');
    Storage::disk('public')->assertExists($document->document_file);

    $this->actingAs($user)
        ->from(route('shipments.show', $shipment))
        ->put(route('shipment-documents.update', $document), [
            'verification_status' => 'complete',
        ])
        ->assertRedirect();

    expect($document->fresh()->verification_status)->toBe('complete');
});

test('tracking updates are included in shipment status history', function () {
    $user = User::factory()->create();
    $shipment = Shipments::query()->create([
        'shipment_number' => 'SHP-TRACK-WORKFLOW-001',
        'tracking_status' => 'not shipped',
    ]);

    $this->actingAs($user)
        ->patch(route('shipments.tracking-status.update', $shipment), [
            'tracking_status' => 'in transit',
            'status_date' => '2026-11-22',
            'location' => 'Singapore',
            'country' => 'Singapore',
            'remarks' => 'Vessel departed.',
        ])
        ->assertRedirect();

    expect($shipment->fresh()->tracking_status)->toBe('in transit')
        ->and($shipment->statusHistories()->first()->status)->toBe('Tracking status: in transit');
});
