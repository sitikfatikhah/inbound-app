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
        Schema::create('shipment_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_name')->nullable();
            $table->timestamp('event_time')->nullable();
            $table->enum('status', ['In Transit', 'Delivered', 'Pending'])->default('Pending');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_locations');
    }
};
