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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 50)->unique();
            $table->foreignId('forwarder_id')->nullable()->constrained('forwarders')->cascadeOnDelete();
            $table->foreignId('purchase_orders_id')->nullable()->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('shipping_line_id')->nullable()->constrained('shipping_lines')->cascadeOnDelete();
            $table->foreignId('port_of_loading_id')->nullable()->constrained('ports')->cascadeOnDelete();
            $table->foreignId('port_of_discharge_id')->nullable()->constrained('ports')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->cascadeOnDelete();
            $table->string('invoice_number')->nullable();
            $table->string('bl_number')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('voyage_number')->nullable();
            $table->date('eta')->nullable();
            $table->date('etd')->nullable();
            $table->date('atd')->nullable();
            $table->date('ata')->nullable();
            $table->date('original_etd')->nullable();
            $table->date('original_eta')->nullable();
            $table->string('status_document')->default('purchase order created')->nullable();
            $table->string('tracking_status')->default('not shipped')->nullable();
            $table->longText('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
