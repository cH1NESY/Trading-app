<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    /**
     * Отменить заказ и вернуть товар на склад
     *
     * @param Order $order Заказ для отмены
     * @return Order
     * @throws \RuntimeException Если заказ нельзя отменить
     */
    public function execute(Order $order): Order
    {
        // Проверяем, можно ли отменить заказ
        if (!$order->canBeCanceled()) {
            throw new \RuntimeException('Заказ не может быть отменен');
        }
        // В транзакции возвращаем товар на склад и меняем статус заказа
        return DB::transaction(function () use ($order) {
            // Для каждой позиции возвращаем товар на склад и создаем движение
            foreach ($order->items as $item) {
                Stock::safeIncreaseStock($item->product_id, $order->warehouse_id, $item->count);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'quantity' => $item->count,
                    'type' => 'order_cancel_return',
                    'description' => 'Возврат товара при отмене заказа',
                ]);
            }
            // Обновляем статус заказа
            $order->update(['status' => Order::STATUS_CANCELED]);
            // Возвращаем заказ с подгруженными связями
            return $order->fresh(['warehouse', 'items.product']);
        });
    }
}
