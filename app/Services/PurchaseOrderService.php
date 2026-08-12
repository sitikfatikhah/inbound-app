<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\PurchaseOrderServiceInterface;
use App\Models\PurchaseOrders;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): PurchaseOrders
    {
        $purchaseOrder = PurchaseOrders::create($data);

        $this->activity->log(
            module: 'PurchaseOrder',
            action: 'CREATE',
            model: $purchaseOrder,
            new: $purchaseOrder->toArray(),
            description: 'Purchase order berhasil dibuat',
        );

        return $purchaseOrder;
    }

    public function update(PurchaseOrders $purchaseOrder, array $data): PurchaseOrders
    {
        $old = $purchaseOrder->getOriginal();

        $purchaseOrder->update($data);

        $this->activity->log(
            module: 'PurchaseOrder',
            action: 'UPDATE',
            model: $purchaseOrder,
            old: $old,
            new: $purchaseOrder->fresh()->toArray(),
            description: 'Purchase order diperbarui'
        );

        return $purchaseOrder->fresh();
    }

    public function delete(PurchaseOrders $purchaseOrder): bool
    {
        $old = $purchaseOrder->toArray();

        $this->activity->log(
            module: 'PurchaseOrder',
            action: 'DELETE',
            model: $purchaseOrder,
            old: $old,
            description: 'Purchase order dihapus'
        );
        return $purchaseOrder->delete();
    }
}