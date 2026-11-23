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
            $table->enum('penjaluran', ['Hijau', 'Merah'])->nullable();
            $table->string('customs_status')->nullable();
            $table->string('attachment_path')->nullable();
            $table->dateTime('inspection_start')->nullable();
            $table->dateTime('inspection_end')->nullable();
            $table->dateTime('inspection_duration')->nullable();
            $table->longText('remarks')->nullable();
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
