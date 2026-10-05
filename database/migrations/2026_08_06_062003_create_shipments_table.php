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
            $table->string('shipment_number')->unique();
            $table->foreignId('forwarder_id')->constrained('forwarders')->onDelete('cascade')->nullable;
            $table->foreignId('shipping_line_id')->constrained('shipping_lines')->onDelete('cascade')->nullable;
            $table->foreignId('port_of_loading_id')->constrained('ports')->onDelete('cascade')->nullable;
            $table->foreignId('port_of_discharge_id')->constrained('ports')->onDelete('cascade')->nullable;
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade')->nullable;
            $table->string('bl_number')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('voyage_number')->nullable();
            $table->date('eta')->nullable();
            $table->date('etd')->nullable();
            $table->date('atd')->nullable();
            $table->date('ata')->nullable();
            $table->string('status_document')->default('purchase order created')->nullable;
            $table->string('tracking_status')->default('not shipped')->nullable;
            $table->longText('remarks')->nullable;
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
