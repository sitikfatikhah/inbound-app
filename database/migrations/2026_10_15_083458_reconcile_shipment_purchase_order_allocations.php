<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->foreignId('forwarder_id')->nullable()->change();
            $table->foreignId('purchase_orders_id')->nullable()->change();
            $table->foreignId('shipping_line_id')->nullable()->change();
            $table->foreignId('port_of_loading_id')->nullable()->change();
            $table->foreignId('port_of_discharge_id')->nullable()->change();
            $table->foreignId('warehouse_id')->nullable()->change();
            $table->string('status_document')->nullable()->default('purchase order created')->change();
            $table->string('tracking_status')->nullable()->default('not shipped')->change();
            $table->longText('remarks')->nullable()->change();
        });

        Schema::table('shipment_containers', function (Blueprint $table): void {
            $table->string('product_code', 100)->change();
        });

        $timestamp = now();

        DB::table('shipments')
            ->whereNotNull('purchase_orders_id')
            ->get(['id', 'purchase_orders_id'])
            ->each(function (object $shipment) use ($timestamp): void {
                $this->insertPurchaseOrderRelation(
                    (int) $shipment->id,
                    (int) $shipment->purchase_orders_id,
                    $timestamp
                );
            });

        DB::table('shipment_containers as allocations')
            ->join(
                'purchase_order_items as items',
                'items.id',
                '=',
                'allocations.purchase_order_item_id'
            )
            ->select('allocations.shipment_id', 'items.purchase_order_id')
            ->distinct()
            ->get()
            ->each(function (object $relation) use ($timestamp): void {
                $this->insertPurchaseOrderRelation(
                    (int) $relation->shipment_id,
                    (int) $relation->purchase_order_id,
                    $timestamp
                );
            });

        DB::table('purchase_order_items')
            ->select('id', 'quantity')
            ->orderBy('id')
            ->chunkById(200, function ($items): void {
                $allocationTotals = DB::table('shipment_containers')
                    ->whereIn('purchase_order_item_id', $items->pluck('id'))
                    ->selectRaw('purchase_order_item_id, SUM(quantity) as allocated_quantity')
                    ->groupBy('purchase_order_item_id')
                    ->pluck('allocated_quantity', 'purchase_order_item_id');

                foreach ($items as $item) {
                    $allocated = (float) ($allocationTotals->get($item->id) ?? 0);

                    DB::table('purchase_order_items')
                        ->where('id', $item->id)
                        ->update([
                            'remaining_qty' => max(0, (float) ($item->quantity ?? 0) - $allocated),
                            'total_allocated' => $allocated,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfilled balances and relations are not safely reversible.
    }

    private function insertPurchaseOrderRelation(int $shipmentId, int $purchaseOrderId, mixed $timestamp): void
    {
        $relation = DB::table('shipment_purchase_orders')
            ->where('shipment_id', $shipmentId)
            ->where('purchase_order_id', $purchaseOrderId);

        if (! $relation->exists()) {
            $relation->insert([
                'shipment_id' => $shipmentId,
                'purchase_order_id' => $purchaseOrderId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
};
