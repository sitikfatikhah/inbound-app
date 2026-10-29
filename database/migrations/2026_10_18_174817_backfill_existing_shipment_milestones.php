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
        DB::table('shipments')->orderBy('id')->chunkById(200, function ($shipments): void {
            foreach ($shipments as $shipment) {
                $purchaseOrderIds = DB::table('shipment_purchase_orders')
                    ->where('shipment_id', $shipment->id)
                    ->pluck('purchase_order_id')
                    ->all();

                if ($shipment->purchase_orders_id && ! in_array($shipment->purchase_orders_id, $purchaseOrderIds)) {
                    $purchaseOrderIds[] = $shipment->purchase_orders_id;
                }

                foreach (array_unique($purchaseOrderIds) as $purchaseOrderId) {
                    $purchaseOrder = DB::table('purchase_orders')->where('id', $purchaseOrderId)->first();

                    if ($purchaseOrder) {
                        $this->insertMilestone(
                            (int) $shipment->id,
                            '[System] Purchase Order: '.$purchaseOrder->po_number,
                            'Completed',
                            null,
                            $this->dateOnly($purchaseOrder->po_date),
                        );
                    }
                }

                foreach ([
                    ['name' => 'Estimated Departure (ETD)', 'date' => $shipment->etd],
                    ['name' => 'Estimated Arrival (ETA)', 'date' => $shipment->eta],
                ] as $schedule) {
                    if ($schedule['date']) {
                        $this->insertMilestone(
                            (int) $shipment->id,
                            '[System] '.$schedule['name'],
                            'Planned',
                            $this->dateOnly($schedule['date']),
                            null,
                        );
                    }
                }

                $recordedDate = $this->dateOnly($shipment->updated_at) ?? now()->toDateString();

                if ($shipment->status_document) {
                    $this->insertMilestone(
                        (int) $shipment->id,
                        '[System] Document status: '.$shipment->status_document,
                        'Completed',
                        null,
                        $recordedDate,
                    );
                }

                if ($shipment->tracking_status) {
                    $trackingStatus = strtolower($shipment->tracking_status);
                    $progress = preg_match('/complete|delivered|arrived|received/', $trackingStatus)
                        ? 'Completed'
                        : (preg_match('/not shipped|planned|pending|scheduled|on hold/', $trackingStatus)
                            ? 'Planned'
                            : 'In Progress');
                    $actualDate = $progress === 'Planned'
                        ? null
                        : ($progress === 'Completed'
                            ? ($this->dateOnly($shipment->ata) ?? $recordedDate)
                            : ($this->dateOnly($shipment->atd) ?? $recordedDate));

                    $this->insertMilestone(
                        (int) $shipment->id,
                        '[System] Tracking status: '.$shipment->tracking_status,
                        $progress,
                        $progress === 'Planned' ? $this->dateOnly($shipment->etd) : null,
                        $actualDate,
                    );
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfilled operational history is retained when rolling back this migration.
    }

    private function insertMilestone(
        int $shipmentId,
        string $name,
        string $status,
        ?string $plannedDate,
        ?string $actualDate,
    ): void {
        $exists = DB::table('shipment_milestones')
            ->where('shipment_id', $shipmentId)
            ->where('milestone_name', $name)
            ->exists();

        if (! $exists) {
            DB::table('shipment_milestones')->insert([
                'shipment_id' => $shipmentId,
                'milestone_name' => $name,
                'status' => $status,
                'planned_date' => $plannedDate,
                'actual_date' => $actualDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function dateOnly(?string $value): ?string
    {
        return $value ? substr($value, 0, 10) : null;
    }
};
