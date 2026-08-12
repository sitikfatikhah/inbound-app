<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\ShipmentServiceInterface;
use App\Models\Shipments;

class ShipmentService implements ShipmentServiceInterface
{
    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): Shipments
    {
        $shipment = Shipments::create($data);

        $this->activity->log(
            module: 'Shipment',
            action: 'CREATE',
            model: $shipment,
            new: $shipment->toArray(),
            description: 'Shipment berhasil dibuat',
        );

        return $shipment;
    }

    public function update(Shipments $shipment, array $data): Shipments
    {
        $old = $shipment->getOriginal();

        $shipment->update($data);

        $this->activity->log(
            module: 'Shipment',
            action: 'UPDATE',
            model: $shipment,
            old: $old,
            new: $shipment->fresh()->toArray(),
            description: 'Shipment diperbarui'
        );

        return $shipment->fresh();
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
}