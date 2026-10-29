<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_shipments', function (Blueprint $table) {
            $table->id();

            $table->string('shipment_number')->unique();

            $table->foreignId('purchase_order_id')
                ->constrained('purchase_orders')
                ->cascadeOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->nullOnDelete();

            $table->string('delivery_order_number')->nullable();

            $table->date('etd')->nullable();
            $table->date('atd')->nullable();

            $table->string('status_document')
                ->default('purchase order created');

            $table->string('tracking_status')
                ->default('not shipped');

            $table->longText('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_shipments');
    }
};
