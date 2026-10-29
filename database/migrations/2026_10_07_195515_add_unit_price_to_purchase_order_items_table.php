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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->bigInteger(('purchase_order_id'))->unsigned()->change();
            $table->bigInteger(('product_id'))->unsigned()->change();
            $table->string('product_code', 100)->nullable()->change();
            $table->string('product_name', 50)->nullable()->change();
            $table->string('hs_code', 50)->nullable()->change();
            $table->decimal('quantity', 12, 2)->nullable()->change();
            $table->decimal('backorder_qty', 12, 2)->nullable()->change();
            $table->decimal('remaining_qty', 12, 2)->nullable()->change();
            $table->decimal('total_allocated', 12, 2)->nullable()->change();
            $table->decimal('unit_price', 12, 2)->nullable()->change();
            $table->string('unit', 50)->nullable()->change();
            $table->string('facility', 100)->nullable()->change();
            $table->decimal('import_duty', 12, 2)->nullable()->change();
            $table->decimal('exchange_rate', 12, 4)->nullable()->change();
            $table->decimal('total_price', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting these types could truncate product codes or fractional quantities.
    }
};
