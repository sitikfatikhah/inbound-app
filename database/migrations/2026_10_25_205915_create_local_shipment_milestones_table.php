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
        Schema::create('local_shipment_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_shipment_id')
                ->constrained('local_shipments')
                ->cascadeOnDelete();
            $table->string('milestone_name');
            $table->date('milestone_date')->nullable();
            $table->enum('status', ['Planned', 'In Progress', 'Completed'])->default('Planned');
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['local_shipment_id', 'planned_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_shipment_milestones');
    }
};
