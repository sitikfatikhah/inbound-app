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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('product_code');// diambil dari Products
            $table->integer('product_name');// diambil dari Products
            $table->integer('hs_code');// diambil dari Products
            $table->integer('quantity');// diambil dari Products
            $table->string('unit')->nullable(); // diambil dari Products
            $table->string('facility')->nullable(); // diambil dari Products
            $table->decimal('import_duty', 20, 6)->nullable();// diambil dari Products
            $table->decimal('exchange_rate', 20, 6)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
