<?php

namespace App\Contracts;

use App\Models\Import;

interface ShipmentserviceInterface
{
    public function create(array $data): Import;

    public function update(Shipments $shipment, array $data): Shipments;

    public function delete(Shipments $shipment): bool;
}