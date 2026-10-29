<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticles;
use App\Models\LocalShipments;
use App\Models\PurchaseOrders;
use App\Models\ShipmentContainers;
use App\Models\Shipments;
use App\Models\Tracking;
use App\Models\WarehouseReceipts;
use Inertia\Inertia;

class TrackingController extends Controller
{
    public function index()
    {
        $trackings = Tracking::all();

        return Inertia::render('tracking/index', compact('trackings'));
    }

    public function import()
    {
        return Inertia::render('tracking/import', [
            'shipments' => Shipments::with([
                'forwarder:id,forwarder_name',
                'shippingLine:id,shipping_line_name',
                'portOfLoading:id,port_name',
                'portOfDischarge:id,port_name',
                'warehouse:id,warehouse_name',
                'milestones' => fn ($query) => $query->orderBy('planned_date'),
            ])->latest()->get(),
        ]);
    }

    public function local()
    {
        return Inertia::render('tracking/local', [
            'localShipments' => LocalShipments::query()
                ->with([
                    'purchaseOrder:id,po_number,supplier_id,po_date,delivery_date,arrival_date,category,status',
                    'purchaseOrder.supplier:id,supplier_name,country',
                    'warehouse:id,warehouse_name',
                    'milestones' => fn ($query) => $query->orderBy('planned_date')->orderBy('id'),
                ])
                ->whereHas('purchaseOrder', fn ($query) => $query->whereRaw('LOWER(category) = ?', ['local']))
                ->latest()
                ->get(),
        ]);
    }

    public function schedule()
    {
        return Inertia::render('tracking/schedule', [
            'shipments' => Shipments::with([
                'portOfLoading:id,port_name',
                'portOfDischarge:id,port_name',
                'purchaseOrders:id,po_number',
                'purchaseOrders.items:id,purchase_order_id,product_id',
                'purchaseOrders.items.product:id,category',
            ])->whereNotNull('eta')->orderBy('eta')->get(),
        ]);
    }

    public function warehouse()
    {
        return Inertia::render('tracking/warehouse', [
            'receipts' => WarehouseReceipts::with([
                'shipment:id,shipment_number,eta,tracking_status',
                'warehouse:id,warehouse_name',
            ])->latest()->get(),
        ]);
    }

    public function analytics()
    {
        return Inertia::render('public/analytics', [
            'purchaseOrders' => PurchaseOrders::query()->latest()->get(['id', 'category', 'po_date', 'arrival_date', 'status']),
            'shipments' => Shipments::query()->latest()->get(['id', 'eta', 'ata', 'tracking_status']),
        ]);
    }

    public function project()
    {
        return Inertia::render('tracking/project', [
            'projects' => ShipmentContainers::query()
                ->with([
                    'shipment:id,shipment_number,etd,eta,ata,tracking_status',
                    'purchaseOrderItem:id,product_id,product_name',
                    'purchaseOrderItem.product:id,product_name,category',
                ])
                ->whereHas('purchaseOrderItem.product', fn ($query) => $query->whereRaw('LOWER(category) = ?', ['sample']))
                ->latest()
                ->get()
                ->map(function (ShipmentContainers $shipmentItem): array {
                    $product = $shipmentItem->purchaseOrderItem?->product;
                    $shipment = $shipmentItem->shipment;
                    $isCompleted = $shipment?->ata || preg_match('/complete|delivered|arrived/i', $shipment?->tracking_status ?? '');

                    return [
                        'id' => $shipmentItem->id,
                        'project_name' => $product?->product_name ?? $shipmentItem->purchaseOrderItem?->product_name ?? $shipmentItem->product_code,
                        'description' => $product?->category,
                        'start_date' => $shipment?->etd?->format('Y-m-d'),
                        'target_date' => $shipment?->eta?->format('Y-m-d'),
                        'pic' => null,
                        'progress' => $isCompleted ? 'Completed' : ($shipment?->tracking_status ? 'In Progress' : 'Not Started'),
                        'quantity' => (float) $shipmentItem->quantity,
                        'product' => [
                            'product_name' => $product?->product_name ?? $shipmentItem->purchaseOrderItem?->product_name,
                            'category' => $product?->category,
                        ],
                        'shipment' => $shipment ? [
                            'id' => $shipment->id,
                            'shipment_number' => $shipment->shipment_number,
                            'tracking_status' => $shipment->tracking_status,
                        ] : null,
                    ];
                })
                ->values(),
        ]);
    }

    public function knowledge()
    {
        return Inertia::render('public/knowledge-mgm', [
            'articles' => KnowledgeArticles::query()
                ->where('is_published', true)
                ->latest()
                ->get(),
        ]);
    }
}
