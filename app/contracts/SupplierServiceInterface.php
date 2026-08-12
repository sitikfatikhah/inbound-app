<?php

namespace App\Contracts;

use App\Models\Suppliers;

interface SupplierServiceInterface
{
    public function create(array $data): Suppliers;

    public function update(Suppliers $shipment, array $data): Suppliers;

    public function delete(Suppliers $shipment): bool;
}