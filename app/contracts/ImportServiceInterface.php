<?php

namespace App\Contracts;

use App\Models\Shipments;

interface ShipmentserviceInterface
{
    public function create(array $data): Shipments;

    public function update(Shipments $shipment, array $data): Shipments;

    public function delete(Shipments $shipment): bool;
}