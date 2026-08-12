<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\SupplierServiceInterface;
use App\Models\Suppliers;

class SupplierService implements SupplierServiceInterface
{
    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): Suppliers
    {
        $supplier = Suppliers::create($data);

        $this->activity->log(
            module: 'Supplier',
            action: 'CREATE',
            model: $supplier,
            new: $supplier->toArray(),
            description: 'Supplier berhasil dibuat',
        );

        return $supplier;
    }

    public function update(Suppliers $supplier, array $data): Suppliers
    {
        $old = $supplier->getOriginal();

        $supplier->update($data);

        $this->activity->log(
            module: 'Supplier',
            action: 'UPDATE',
            model: $supplier,
            old: $old,
            new: $supplier->fresh()->toArray(),
            description: 'Supplier diperbarui'
        );

        return $supplier->fresh();
    }

    public function delete(Suppliers $supplier): bool
    {
        $old = $supplier->toArray();

        $this->activity->log(
            module: 'Supplier',
            action: 'DELETE',
            model: $supplier,
            old: $old,
            description: 'Supplier dihapus'
        );
        
        return $supplier->delete();
    }
}