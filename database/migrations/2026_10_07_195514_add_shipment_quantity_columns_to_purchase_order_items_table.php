<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table): void {
            $table->decimal('backorder_qty', 12, 2)->nullable();
            $table->decimal('remaining_qty', 12, 2)->nullable();
            $table->decimal('total_allocated', 12, 2)->nullable();
        });

        DB::table('purchase_order_items')->update([
            'backorder_qty' => DB::raw('COALESCE(backorder_qty, quantity, 0)'),
            'remaining_qty' => DB::raw('COALESCE(remaining_qty, quantity, 0)'),
            'total_allocated' => DB::raw('COALESCE(total_allocated, 0)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table): void {
            $table->dropColumn(['backorder_qty', 'remaining_qty', 'total_allocated']);
        });
    }
};
