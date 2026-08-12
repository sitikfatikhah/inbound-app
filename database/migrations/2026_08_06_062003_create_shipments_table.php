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
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('forwarder_id')->constrained('forwarders')->onDelete('cascade');
            $table->foreignId('shipping_line_id')->constrained('shipping_lines')->onDelete('cascade');
            $table->foreignId('port_of_loading_id')->constrained('ports')->onDelete('cascade');
            $table->foreignId('port_of_discharge_id')->constrained('ports')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('bl_number')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('voyage_number')->nullable();
            $table->date('eta')->nullable();
            $table->date('etd')->nullable();
            $table->date('etd_actual')->nullable();
            $table->date('eta_actual')->nullable();
            $table->date('ata')->nullable();
            $table->string('shipment_number')->unique();
            $table->date('shipment_date');
            $table->string('status')->default('purchase order created');
            $table->string('tracking_status')->default('not shipped');
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
