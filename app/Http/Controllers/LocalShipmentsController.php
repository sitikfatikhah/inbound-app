<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocalShipmentRequest;
use App\Models\LocalShipments;
use App\Models\PurchaseOrders;
use App\Models\TrackingStatus;
use App\Models\Warehouses;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LocalShipmentsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('local-shipment/index', [
            'localShipments' => LocalShipments::query()
                ->with([
                    'purchaseOrder:id,po_number,supplier_id',
                    'purchaseOrder.supplier:id,supplier_name',
                    'warehouse:id,warehouse_name',
                    'milestones' => fn ($query) => $query->orderBy('planned_date')->orderBy('id'),
                ])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): Response
    {
        $prefix = 'LOCSHP-'.now()->format('Ymd').'-';
        $latestShipmentNumber = LocalShipments::query()
            ->where('shipment_number', 'like', $prefix.'%')
            ->latest('id')
            ->value('shipment_number');
        $nextNumber = $latestShipmentNumber
            ? (int) substr($latestShipmentNumber, -4) + 1
            : 1;

        return Inertia::render('local-shipment/create', [
            'statusOptions' => TrackingStatus::query()
                ->where('category', 'customs')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('name')
                ->values(),

            'trackingStatusOptions' => TrackingStatus::query()
                ->where('category', 'shipment')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('name')
                ->values(),
        ]);

        return Inertia::render('local-shipment/create', [
            'shipment_number' => $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT),
            ...$this->lookupOptions(),
        ]);
    }

    public function store(LocalShipmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $milestones = $data['milestones'] ?? [];
        unset($data['milestones']);

        DB::transaction(function () use ($data, $milestones): void {
            $shipment = LocalShipments::query()->create($data);
            $shipment->milestones()->createMany($milestones);
        });

        return redirect()->route('local-shipment.index')
            ->with('success', 'Local Shipment created successfully.');
    }

    public function show(LocalShipments $local_shipment): Response
    {
        $local_shipment->load([
            'purchaseOrder:id,po_number,supplier_id',
            'purchaseOrder.supplier:id,supplier_name',
            'warehouse:id,warehouse_name',
            'milestones' => fn ($query) => $query->orderBy('planned_date')->orderBy('id'),
        ]);

        return Inertia::render('local-shipment/show', [
            'localShipment' => $local_shipment,
        ]);
    }

    public function edit(LocalShipments $local_shipment): Response
    {
        $local_shipment->load([
            'purchaseOrder:id,po_number,supplier_id',
            'purchaseOrder.supplier:id,supplier_name',
            'warehouse:id,warehouse_name',
            'milestones' => fn ($query) => $query->orderBy('planned_date')->orderBy('id'),
        ]);

        return Inertia::render('local-shipment/edit', [
            'localShipment' => [
                ...$local_shipment->toArray(),
                'etd' => $local_shipment->etd?->format('Y-m-d'),
                'atd' => $local_shipment->atd?->format('Y-m-d'),
            ],
            'milestones' => $local_shipment->milestones->map(fn ($milestone): array => [
                'milestone_name' => $milestone->milestone_name,
                'status' => $milestone->status,
                'planned_date' => $milestone->planned_date?->format('Y-m-d'),
                'actual_date' => $milestone->actual_date?->format('Y-m-d'),
                'remarks' => $milestone->remarks,
            ])->values(),
            ...$this->lookupOptions(),
        ]);
    }

    public function update(LocalShipmentRequest $request, LocalShipments $local_shipment): RedirectResponse
    {
        $data = $request->validated();
        $hasMilestones = array_key_exists('milestones', $data);
        $milestones = $data['milestones'] ?? [];
        unset($data['milestones']);

        DB::transaction(function () use ($data, $hasMilestones, $local_shipment, $milestones): void {
            $local_shipment->update($data);

            if ($hasMilestones) {
                $local_shipment->milestones()->delete();
                $local_shipment->milestones()->createMany($milestones);
            }
        });

        return redirect()->route('local-shipment.index')
            ->with('success', 'Local Shipment updated successfully.');
    }

    public function destroy(LocalShipments $local_shipment): RedirectResponse
    {
        $local_shipment->delete();

        return redirect()->route('local-shipment.index')
            ->with('success', 'Local Shipment deleted successfully.');
    }

    /** @return array{purchaseOrders: Collection<int, PurchaseOrders>, warehouses: Collection<int, Warehouses>} */
    private function lookupOptions(): array
    {
        return [
            'purchaseOrders' => PurchaseOrders::query()
                ->whereRaw('LOWER(category) = ?', ['local'])
                ->select('id', 'po_number', 'supplier_id')
                ->with('supplier:id,supplier_name')
                ->orderBy('po_number')
                ->get(),
            'warehouses' => Warehouses::query()
                ->select('id', 'warehouse_name')
                ->orderBy('warehouse_name')
                ->get(),
        ];
    }
}
