<?php

namespace App\Contracts;

use App\Models\Import;
use App\Models\PurchaseOrders;

interface PurchaseOrderServiceInterface
{
    public function create(array $data): PurchaseOrders;

    public function update(PurchaseOrders $shipment, array $data): PurchaseOrders;

    public function delete(PurchaseOrders $shipment): bool;
}