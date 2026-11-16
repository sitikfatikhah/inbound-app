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
        if (! Schema::hasTable('local_shipment_status_histories')) {
            Schema::create('local_shipment_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('local_shipment_id')
                    ->constrained('local_shipments')
                    ->cascadeOnDelete();
                $table->string('status_type');
                $table->string('status');
                $table->dateTime('status_date');
                $table->timestamps();

                $table->index(
                    ['local_shipment_id', 'status_date'],
                    'local_ship_status_date_idx',
                );
            });
        } else {
            Schema::table('local_shipment_status_histories', function (Blueprint $table) {
                $table->index(
                    ['local_shipment_id', 'status_date'],
                    'local_ship_status_date_idx',
                );
            });
        }

        DB::table('local_shipments')
            ->orderBy('id')
            ->chunkById(200, function ($shipments): void {
                foreach ($shipments as $shipment) {
                    $statusDate = $shipment->updated_at ?? $shipment->created_at ?? now();
                    $statuses = [
                        ['type' => 'document', 'status' => $shipment->status_document],
                        ['type' => 'shipment', 'status' => $shipment->tracking_status],
                    ];

                    foreach ($statuses as $status) {
                        if (! $status['status']) {
                            continue;
                        }

                        DB::table('local_shipment_status_histories')->insert([
                            'local_shipment_id' => $shipment->id,
                            'status_type' => $status['type'],
                            'status' => $status['status'],
                            'status_date' => $statusDate,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_shipment_status_histories');
    }
};
