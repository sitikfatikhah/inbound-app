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
        Schema::create('customs_clearance_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customs_clearance_id')->constrained('customs_clearances')->cascadeOnDelete();
            $table->foreignId('customs_response_type_id')->constrained('customs_response_types')->restrictOnDelete();
            $table->string('document_number')->nullable();
            $table->date('response_date');
            $table->text('action_taken')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['customs_clearance_id', 'response_date'],'ccr_clearance_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customs_clearance_responses');
    }
};
