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
        Schema::create('shipment_customs_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete()->unique();
            $table->string('jalur')->nullable();
            $table->string('pib_number')->nullable();
            $table->date('pib_date')->nullable();
            $table->string('sppb_number')->nullable();
            $table->date('sppb_date')->nullable();
            $table->dateTime('inspection_start')->nullable();
            $table->dateTime('inspection_end')->nullable();
            $table->unsignedInteger('inspection_duration')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_customs_clearances');
    }
};
