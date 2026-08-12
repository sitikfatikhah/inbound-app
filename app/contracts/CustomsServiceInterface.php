<?php

namespace App\Contracts;

use App\Models\CustomsClearances;

interface CustomsServiceInterface
{
    public function create(array $data): CustomsClearances;

    public function update(CustomsClearances $shipment, array $data): CustomsClearances;

    public function delete(CustomsClearances $shipment): bool;
}