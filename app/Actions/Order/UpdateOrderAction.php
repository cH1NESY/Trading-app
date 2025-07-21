<?php

namespace App\Actions\Order;

use App\DTO\OrderUpdateDTO;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class UpdateOrderAction
{
    /**
     * Обновить заказ (клиент, склад, позиции)
     *
     * @param Order $order Заказ для обновления
     * @param OrderUpdateDTO $dto DTO с новыми данными
     * @return Order
     * @throws \RuntimeException Если недостаточно товара на новом складе
     */
    public function execute(Order $order, OrderUpdateDTO $dto): Order
    {
        $newWarehouseId = $dto->warehouse_id ?? $order->warehouse_id;
        $oldWarehouseId = $order->warehouse_id;
        // Проверяем наличие товара на новом складе
        if ($newWarehouseId !== $oldWarehouseId) {
            foreach ($dto->items as $item) {
                $stock = Stock::findByProductAndWarehouse($item->product_id, $newWarehouseId);
                if (!$stock || !$stock->hasEnoughStock($item->count)) {
                    throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на новом складе");
                }
            }
        } else if (!empty($dto->items)) {
            // Если склад не меняется, проверяем остатки на текущем складе
            foreach ($dto->items as $item) {
                $stock = Stock::findByProductAndWarehouse($item->product_id, $order->warehouse_id);
                if (!$stock || !$stock->hasEnoughStock($item->count)) {
                    throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на складе");
                }
            }
        }

        return DB::transaction(function () use ($order, $dto, $newWarehouseId, $oldWarehouseId) {
            // Если склад меняется возвращаем все товары на старый склад
            if ($newWarehouseId !== $oldWarehouseId) {
                foreach ($order->items as $item) {
                    Stock::safeIncreaseStock($item->product_id, $oldWarehouseId, $item->count);
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $oldWarehouseId,
                        'quantity' => $item->count,
                        'type' => 'order_update_return',
                        'description' => 'Возврат товара при изменении склада заказа',
                    ]);
                }
                // Обновляем склад у заказа
                $order->update(['warehouse_id' => $newWarehouseId]);
            }
            // Обновляем клиента, если передан
            if ($dto->customer !== null) {
                $order->update(['customer' => $dto->customer]);
            }
            // Если передан новый список товаров
            if (!empty($dto->items)) {
                if ($newWarehouseId !== $oldWarehouseId) {
                    // Если склад меняется удаляем старые позиции и создаём новые на новом складе
                    $order->items()->delete();
                    foreach ($dto->items as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'count' => $item->count,
                        ]);
                        Stock::safeDecreaseStock($item->product_id, $newWarehouseId, $item->count);
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => $newWarehouseId,
                            'quantity' => -$item->count,
                            'type' => 'order_update_decrease',
                            'description' => 'Списание при изменении склада заказа',
                        ]);
                    }
                } else {
                    // Если склад не меняется возвращаем старые позиции, удаляем их и создаём новые
                    foreach ($order->items as $item) {
                        Stock::safeIncreaseStock($item->product_id, $order->warehouse_id, $item->count);
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => $order->warehouse_id,
                            'quantity' => $item->count,
                            'type' => 'order_update_return',
                            'description' => 'Возврат товара при изменении склада заказа',
                        ]);
                    }
                    $order->items()->delete();
                    foreach ($dto->items as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'count' => $item->count,
                        ]);
                        Stock::safeDecreaseStock($item->product_id, $order->warehouse_id, $item->count);
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => $order->warehouse_id,
                            'quantity' => -$item->count,
                            'type' => 'order_update_decrease',
                            'description' => 'Списание при изменении склада заказа',
                        ]);
                    }
                }
            }
            // Возвращаем обновлённый заказ с подгруженными связями
            return $order->fresh(['warehouse', 'items.product']);
        });
    }
}
