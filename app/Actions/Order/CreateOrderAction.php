<?php

namespace App\Actions\Order;

use App\DTO\OrderCreateDTO;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    /**
     * Создать новый заказ
     *
     * @param OrderCreateDTO $dto DTO с данными для создания заказа
     * @return Order
     * @throws \RuntimeException Если недостаточно товара на складе
     */
    public function execute(OrderCreateDTO $dto): Order
    {
        // Проверяем наличие товара на складе и если товара нет или его недостаточно — выбрасываем исключение
        foreach ($dto->items as $item) {
            $stock = Stock::findByProductAndWarehouse($item->product_id, $dto->warehouse_id);
            if (!$stock || !$stock->hasEnoughStock($item->count)) {
                throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на складе");
            }
        }
        // В транзакции создаём заказ, для каждого товара создаём позицию заказа, списываем остатки и создаём движение
        return DB::transaction(function () use ($dto) {
            $order = Order::create([
                'customer' => $dto->customer,
                'warehouse_id' => $dto->warehouse_id,
                'status' => Order::STATUS_ACTIVE,
            ]);
            foreach ($dto->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'count' => $item->count,
                ]);
                Stock::safeDecreaseStock($item->product_id, $dto->warehouse_id, $item->count);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $dto->warehouse_id,
                    'quantity' => -$item->count,
                    'type' => 'order_create',
                    'description' => 'Списание при создании заказа',
                ]);
            }
            // Возвращаем заказ с подгруженными связями
            return $order->fresh(['warehouse', 'items.product']);
        });
    }
}
