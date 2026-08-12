<?php

namespace App\Contracts;

use App\Models\Shipments;

interface ShipmentServiceInterface
{
    public function create(array $data): Shipments;

    public function update(Shipments $shipment, array $data): Shipments;

    public function delete(Shipments $shipment): bool;
}