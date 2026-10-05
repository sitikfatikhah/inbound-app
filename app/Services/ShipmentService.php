<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\ShipmentServiceInterface;
use App\Models\Shipments;
use Illuminate\Support\Facades\DB;

class ShipmentService implements ShipmentServiceInterface
{
    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): Shipments
    {
        return DB::transaction(function () use ($data): Shipments {
            $history = [
                'status' => $data['status_document'],
                'status_date' => now(),
                'location' => $data['location'] ?? null,
                'country' => $data['country'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ];

            $shipment = Shipments::create(
                collect($data)->except(['location', 'country', 'remarks'])->all()
            );

            $shipment->statusHistories()->create($history);

            $this->activity->log(
                module: 'Shipment',
                action: 'CREATE',
                model: $shipment,
                new: $shipment->toArray(),
                description: 'Shipment berhasil dibuat',
            );

            return $shipment->load(['statusHistories', 'milestones']);
        });
    }

    public function update(Shipments $shipment, array $data): Shipments
    {
        return DB::transaction(function () use ($shipment, $data): Shipments {
            $old = $shipment->getOriginal();
            $statusChanged = $shipment->status_document !== $data['status_document'];
            $history = [
                'status' => $data['status_document'],
                'status_date' => now(),
                'location' => $data['location'] ?? null,
                'country' => $data['country'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ];

            $shipment->update(
                collect($data)->except(['location', 'country', 'remarks'])->all()
            );

            if ($statusChanged) {
                $shipment->statusHistories()->create($history);
            }

            $this->activity->log(
                module: 'Shipment',
                action: 'UPDATE',
                model: $shipment,
                old: $old,
                new: $shipment->fresh()->toArray(),
                description: 'Shipment diperbarui'
            );

            return $shipment->fresh(['statusHistories', 'milestones']);
        });
    }

    public function delete(Shipments $shipment): bool
    {
        $old = $shipment->toArray();

        $this->activity->log(
            module: 'Shipment',
            action: 'DELETE',
            model: $shipment,
            old: $old,
            description: 'Shipment dihapus'
        );

        return $shipment->delete();
    }

    public function generateShipmentNumber(): string
    {
        $latestShipment = Shipments::latest()->first();

        if ($latestShipment) {
            $lastNumber = (int) substr($latestShipment->shipment_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'SHP-'.date('Ymd').'-'.$newNumber;
    }
}
