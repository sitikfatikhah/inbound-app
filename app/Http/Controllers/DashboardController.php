<?php

namespace App\Http\Controllers;

use App\Models\CustomsClearances;
use App\Models\Shipments;
use App\Models\WarehouseReceipts;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = Carbon::today();
        $weekEnd = $today->copy()->endOfWeek();

        /*
        |--------------------------------------------------------------------------
        | 1. TOTAL & ACTIVE SHIPMENT
        |--------------------------------------------------------------------------
        |
        | Shipment dianggap selesai dari sisi transport apabila sudah memiliki ATA.
        |
        */

        $totalShipments = Shipments::query()->count();

        $activeShipmentsQuery = Shipments::query()
            ->whereNull('ata');

        $activeShipments = (clone $activeShipmentsQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | 2. ARRIVAL ETA
        |--------------------------------------------------------------------------
        |
        | arrivalsToday      = ETA hari ini
        | arrivalsThisWeek   = ETA dari hari ini sampai akhir minggu
        |
        */

        $arrivalsToday = Shipments::query()
            ->whereDate('eta', $today)
            ->count();

        $arrivalsThisWeek = Shipments::query()
            ->whereBetween('eta', [
                $today->copy()->startOfDay(),
                $weekEnd->endOfDay(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 3. DELAYED SHIPMENT
        |--------------------------------------------------------------------------
        |
        | Shipment terlambat apabila:
        |
        | - belum memiliki ATA
        | - ETA sudah lewat hari ini
        |
        */

        $delayedShipmentsQuery = Shipments::query()
            ->whereNull('ata')
            ->whereNotNull('eta')
            ->whereDate('eta', '<', $today);

        $delayed = (clone $delayedShipmentsQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | 4. CUSTOMS CLEARANCE PENDING
        |--------------------------------------------------------------------------
        |
        | Ambil shipment yang mempunyai customs clearance
        | tetapi belum memiliki customs_clearance_date.
        |
        | DISTINCT digunakan agar 1 shipment tidak dihitung lebih dari 1 kali.
        |
        */

        $clearancePendingShipmentIds = CustomsClearances::query()
            ->whereNull('customs_clearance_date')
            ->whereNotNull('shipment_id')
            ->select('shipment_id')
            ->distinct();

        $pendingClearance = (clone $clearancePendingShipmentIds)->count();

        /*
        |--------------------------------------------------------------------------
        | 5. WAREHOUSE RECEIVED
        |--------------------------------------------------------------------------
        |
        | Shipment dianggap sudah diterima apabila:
        |
        | - mempunyai Warehouse Receipt
        | - status = Received
        | - received_date terisi
        |
        */

        $receivedShipmentIds = WarehouseReceipts::query()
            ->where('status', 'Received')
            ->whereNotNull('shipment_id')
            ->whereNotNull('received_date')
            ->select('shipment_id')
            ->distinct();

        /*
        |--------------------------------------------------------------------------
        | 6. NOT RECEIVED
        |--------------------------------------------------------------------------
        |
        | Shipment yang belum memiliki Warehouse Receipt "Received".
        |
        */

        $notReceived = Shipments::query()
            ->whereNotIn('id', $receivedShipmentIds)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 7. TIMELINE 7 HARI
        |--------------------------------------------------------------------------
        |
        | target = ETA
        | actual = ATA
        |
        */

        $timelineStart = $today->copy()->startOfDay();
        $timelineEnd = $today->copy()->addDays(6)->endOfDay();

        $etaRows = Shipments::query()
            ->selectRaw('DATE(eta) as shipment_date, COUNT(*) as total')
            ->whereNotNull('eta')
            ->whereBetween('eta', [
                $timelineStart,
                $timelineEnd,
            ])
            ->groupByRaw('DATE(eta)')
            ->orderBy('shipment_date')
            ->get()
            ->keyBy('shipment_date');

        $ataRows = Shipments::query()
            ->selectRaw('DATE(ata) as shipment_date, COUNT(*) as total')
            ->whereNotNull('ata')
            ->whereBetween('ata', [
                $timelineStart,
                $timelineEnd,
            ])
            ->groupByRaw('DATE(ata)')
            ->orderBy('shipment_date')
            ->get()
            ->keyBy('shipment_date');

        $timeline = collect(range(0, 6))
            ->map(function (int $offset) use (
                $today,
                $etaRows,
                $ataRows
            ): array {
                $date = $today->copy()->addDays($offset);
                $key = $date->toDateString();

                return [
                    'label' => $date->translatedFormat('D'),
                    'date' => $key,
                    'target' => (int) ($etaRows[$key]->total ?? 0),
                    'actual' => (int) ($ataRows[$key]->total ?? 0),
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | 8. STATUS BREAKDOWN
        |--------------------------------------------------------------------------
        |
        | Status berdasarkan data aktual shipment.
        |
        | Terjadwal:
        |   Belum ATD tetapi sudah mempunyai ETA.
        |
        | Dalam perjalanan:
        |   Sudah ATD tetapi belum ATA.
        |
        | Terlambat:
        |   ETA sudah lewat dan belum ATA.
        |
        | Sudah diterima:
        |   Sudah memiliki Warehouse Receipt Received.
        |
        */

        $inTransitCount = Shipments::query()
            ->whereNull('ata')
            ->whereNotNull('atd')
            ->where(function (Builder $query) use ($today): void {
                $query
                    ->whereNull('eta')
                    ->orWhereDate('eta', '>=', $today);
            })
            ->count();

        $scheduledCount = Shipments::query()
            ->whereNull('ata')
            ->whereNull('atd')
            ->whereNotNull('eta')
            ->whereDate('eta', '>=', $today)
            ->count();

        $receivedCount = Shipments::query()
            ->whereIn('id', $receivedShipmentIds)
            ->count();

        $statusBreakdown = collect([
            [
                'key' => 'in_transit',
                'label' => 'Dalam perjalanan',
                'color' => 'success',
                'count' => $inTransitCount,
            ],
            [
                'key' => 'scheduled',
                'label' => 'Terjadwal',
                'color' => 'primary',
                'count' => $scheduledCount,
            ],
            [
                'key' => 'delayed',
                'label' => 'Terlambat',
                'color' => 'error',
                'count' => $delayed,
            ],
            [
                'key' => 'received',
                'label' => 'Sudah diterima',
                'color' => 'success',
                'count' => $receivedCount,
            ],
        ])->values();

        /*
        |--------------------------------------------------------------------------
        | 9. EXCEPTIONS - DELAYED
        |--------------------------------------------------------------------------
        */

        $exceptionLimit = 8;

        $delayedShipments = (clone $delayedShipmentsQuery)
            ->with([
                'forwarder:id,forwarder_name',
                'purchaseOrders' => fn ($query) => $query->with([
                    'supplier:id,supplier_name',
                    'items.product:id,product_name',
                ]),
            ])
            ->orderBy('eta')
            ->limit($exceptionLimit)
            ->get();

        $exceptions = $delayedShipments
            ->map(
                fn (Shipments $shipment): array => $this->exceptionResource(
                    $shipment,
                    'Terlambat',
                    'Review ETA'
                )
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 10. EXCEPTIONS - CUSTOMS CLEARANCE
        |--------------------------------------------------------------------------
        */

        $remainingExceptionSlots = max(
            0,
            $exceptionLimit - $exceptions->count()
        );

        if ($remainingExceptionSlots > 0) {
            $clearanceShipments = Shipments::query()
                ->whereNull('ata')
                ->whereIn('id', $clearancePendingShipmentIds)
                ->with([
                    'forwarder:id,forwarder_name',
                    'purchaseOrders' => fn ($query) => $query->with([
                        'supplier:id,supplier_name',
                        'items.product:id,product_name',
                    ]),
                ])
                ->latest()
                ->limit($remainingExceptionSlots)
                ->get();

            $clearanceExceptions = $clearanceShipments
                ->map(
                    fn (Shipments $shipment): array => $this->exceptionResource(
                        $shipment,
                        'Menunggu clearance',
                        'Selesaikan'
                    )
                )
                ->values();

            $exceptions = $exceptions
                ->concat($clearanceExceptions)
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 11. INERTIA RESPONSE
        |--------------------------------------------------------------------------
        */

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalShipments' => $totalShipments,
                'activeShipments' => $activeShipments,
                'arrivalsToday' => $arrivalsToday,
                'arrivalsThisWeek' => $arrivalsThisWeek,
                'delayed' => $delayed,
                'pendingClearance' => $pendingClearance,
                'notReceived' => $notReceived,
            ],

            'timeline' => $timeline,

            'statusBreakdown' => $statusBreakdown,

            'exceptions' => $exceptions,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function exceptionResource(
        Shipments $shipment,
        string $status,
        string $action
    ): array {
        $purchaseOrder = $shipment->purchaseOrders->first();

        $product = $purchaseOrder?->items
            ->first()
            ?->product;

        return [
            'id' => $shipment->id,

            'reference' => $shipment->shipment_number,

            'detail' => $shipment->bl_number
                ?: ($shipment->vessel_name ?: 'Tanpa detail'),

            'product' => $product?->product_name
                ?: 'Produk belum ditautkan',

            'vendor' => $purchaseOrder?->supplier?->supplier_name
                ?: 'Vendor belum ditautkan',

            'status' => $status,

            'eta' => $shipment->eta?->toDateString(),

            'action' => $action,
        ];
    }
}