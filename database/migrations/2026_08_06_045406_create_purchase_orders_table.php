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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('Suppliers')->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('po_number', 100);
            $table->dateTime('po_date');
            $table->string('currency', 10)->nullable();
            $table->decimal('exchange_rate', 10, 2)->nullable();
            $table->string('incoterm', 20)->nullable();
            $table->string('payment_term', 100)->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->dateTime('arrival_date')->nullable();
            $table->boolean('status')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
