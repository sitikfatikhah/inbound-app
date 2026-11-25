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
        Schema::table('customs_clearance_responses', function (Blueprint $table) {
            $table->text('action_required')->nullable()->after('response_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customs_clearance_responses', function (Blueprint $table) {
            $table->dropColumn('action_required');
        });
    }
};
