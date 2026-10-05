<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticles;
use App\Models\ProductProjects;
use App\Models\PurchaseOrders;
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
            'purchaseOrders' => PurchaseOrders::with([
                'supplier',
                'items',
            ])->latest()->get(),
        ]);
    }

    public function schedule()
    {
        return Inertia::render('tracking/schedule', [
            'shipments' => Shipments::with([
                'forwarder:id,forwarder_name',
                'shippingLine:id,shipping_line_name',
                'portOfLoading:id,port_name',
                'portOfDischarge:id,port_name',
                'warehouse:id,warehouse_name',
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
            'projects' => ProductProjects::with([
                'product:id,product_name',
                'shipment:id,shipment_number',
            ])->latest()->get(),
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
