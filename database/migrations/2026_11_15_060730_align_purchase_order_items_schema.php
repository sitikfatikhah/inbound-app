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
        $hasUnitPrice = Schema::hasColumn('purchase_order_items', 'unit_price');

        Schema::table('purchase_order_items', function (Blueprint $table) use ($hasUnitPrice): void {
            if (! $hasUnitPrice) {
                $table->decimal('unit_price', 12, 2)->nullable();
            }

            $table->string('product_code', 100)->nullable()->change();
            $table->string('product_name', 255)->nullable()->change();
            $table->decimal('quantity', 12, 2)->nullable()->change();
            $table->string('unit')->nullable()->change();
            $table->string('facility')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing item values cannot be safely converted back to the narrower legacy types.
    }
};
