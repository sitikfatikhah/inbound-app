<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\PurchaseOrderServiceInterface;
use App\Models\PurchaseOrders;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): PurchaseOrders
    {
        $items = $data['items'];
        unset($data['items']);

        $purchaseOrder = DB::transaction(function () use ($data, $items): PurchaseOrders {
            $purchaseOrder = PurchaseOrders::create($data);
            $this->replaceItems($purchaseOrder, $items);

            return $purchaseOrder->load('items');
        });

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
        $items = $data['items'];
        unset($data['items']);
        $old = $purchaseOrder->load('items')->toArray();

        $purchaseOrder = DB::transaction(function () use ($purchaseOrder, $data, $items): PurchaseOrders {
            $purchaseOrder->update($data);
            $this->replaceItems($purchaseOrder, $items);

            return $purchaseOrder->fresh(['items']);
        });

        $this->activity->log(
            module: 'PurchaseOrder',
            action: 'UPDATE',
            model: $purchaseOrder,
            old: $old,
            new: $purchaseOrder->toArray(),
            description: 'Purchase order diperbarui'
        );

        return $purchaseOrder;
    }

    private function replaceItems(PurchaseOrders $purchaseOrder, array $items): void
    {
        $purchaseOrder->items()->delete();

        foreach ($items as $item) {
            $item['total_price'] = (float) $item['quantity'] * (float) $item['unit_price'];
            $item['backorder_qty'] = (float) $item['quantity'];
            $item['remaining_qty'] = (float) $item['quantity'];
            $item['total_allocated'] = 0;
            $purchaseOrder->items()->create($item);
        }
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
