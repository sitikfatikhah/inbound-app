<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('purchase_order_items')
            ->whereNull('unit_price')
            ->whereNotNull('total_price')
            ->where('quantity', '>', 0)
            ->update([
                'unit_price' => DB::raw('total_price / quantity'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Derived unit prices cannot be distinguished from values saved by users.
    }
};
