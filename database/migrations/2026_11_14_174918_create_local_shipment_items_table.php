<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('local_shipment_items')) {
            Schema::create('local_shipment_items', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('local_shipment_id')->constrained('local_shipments')->cascadeOnDelete();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
                $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->cascadeOnDelete();
                $table->decimal('quantity', 12, 2);
                $table->timestamps();

                $table->unique(
                    ['local_shipment_id', 'purchase_order_item_id'],
                    'local_shipment_items_shipment_item_unique'
                );
                $table->index('purchase_order_id');
            });

            return;
        }

        if (! Schema::hasIndex(
            'local_shipment_items',
            ['local_shipment_id', 'purchase_order_item_id'],
            'unique'
        )) {
            Schema::table('local_shipment_items', function (Blueprint $table): void {
                $table->unique(
                    ['local_shipment_id', 'purchase_order_item_id'],
                    'local_shipment_items_shipment_item_unique'
                );
            });
        }

        if (! Schema::hasIndex('local_shipment_items', 'purchase_order_id')) {
            Schema::table('local_shipment_items', function (Blueprint $table): void {
                $table->index('purchase_order_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_shipment_items');
    }
};
