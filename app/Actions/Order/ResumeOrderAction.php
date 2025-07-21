<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ResumeOrderAction
{
    /**
     * Возобновить отменённый заказ (списать товар со склада)
     *
     * @param Order $order Заказ для возобновления
     * @return Order
     * @throws \RuntimeException Если заказ нельзя возобновить или не хватает товара
     */
    public function execute(Order $order): Order
    {
        // Проверяем, можно ли возобновить заказ
        if (!$order->canBeResumed()) {
            throw new \RuntimeException('Заказ не может быть возобновлен');
        }
        // Проверяем наличие товара на складе
        foreach ($order->items as $item) {
            $stock = Stock::findByProductAndWarehouse($item->product_id, $order->warehouse_id);
            if (!$stock || !$stock->hasEnoughStock($item->count)) {
                throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на складе для возобновления заказа");
            }
        }
        // В транзакции списываем товар и меняем статус заказа, создаем движение
        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                Stock::safeDecreaseStock($item->product_id, $order->warehouse_id, $item->count);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'quantity' => -$item->count,
                    'type' => 'order_resume_decrease',
                    'description' => 'Списание при возобновлении заказа',
                ]);
            }
            $order->update(['status' => Order::STATUS_ACTIVE]);
            // Возвращаем заказ с подгруженными связями
            return $order->fresh(['warehouse', 'items.product']);
        });
    }
}
