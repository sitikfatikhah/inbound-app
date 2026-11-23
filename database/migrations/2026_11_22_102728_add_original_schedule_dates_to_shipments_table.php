<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->date('original_eta')->nullable();
            $table->date('original_etd')->nullable();
        });

        DB::table('shipments')->whereNotNull('eta')->update(['original_eta' => DB::raw('eta')]);
        DB::table('shipments')->whereNotNull('etd')->update(['original_etd' => DB::raw('etd')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['original_eta', 'original_etd']);
        });
    }
};
