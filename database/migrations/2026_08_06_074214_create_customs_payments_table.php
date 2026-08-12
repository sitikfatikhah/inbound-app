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
        Schema::create('customs_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customs_clearance_id')->constrained('customs_clearances')->onDelete('cascade');
            $table->decimal('customs_value', 15, 2)->nullable();
            $table->decimal('import_duty', 15, 2)->nullable();
            $table->decimal('import_value', 15, 2)->nullable();
            $table->decimal('vat', 15, 2)->nullable();
            $table->decimal('pph_22_import', 15, 2)->nullable();
            $table->decimal('luxury_tax', 15, 2)->nullable();
            $table->decimal('other_taxes', 15, 2)->nullable();
            $table->decimal('total_import_duty_and_taxes', 15, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('billing_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customs_payments');
    }
};
