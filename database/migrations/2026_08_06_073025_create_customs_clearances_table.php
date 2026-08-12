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
        Schema::create('customs_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('pib_number')->nullable();
            $table->date('pib_date')->nullable();
            $table->string('nopen')->nullable();
            $table->date('nopen_date')->nullable();
            $table->string('sppb_number')->nullable();
            $table->date('sppb_date')->nullable();
            $table->string('routing_status')->nullable();
            $table->string('inspection_status')->nullable();
            $table->date('inspection_date')->nullable();
            $table->date('customs_clearance_date')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customs_clearances');
    }
};
