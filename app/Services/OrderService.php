<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\DTO\OrderCreateDTO;
use App\DTO\OrderUpdateDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\DTO\OrderFilterDTO;

class OrderService
{
    public function createOrder(OrderCreateDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            // Проверяем наличие товаров на складе
            foreach ($dto->items as $item) {
                $stock = Stock::findOrCreate($item->product_id, $dto->warehouse_id, 0);
                if (!$stock->hasEnoughStock($item->count)) {
                    throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на складе");
                }
            }
            // Создаем заказ
            $order = Order::create([
                'customer' => $dto->customer,
                'warehouse_id' => $dto->warehouse_id,
                'status' => Order::STATUS_ACTIVE,
            ]);
            // Создаем позиции заказа и списываем товары
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
            return $order->fresh(['warehouse', 'items.product']);
        });
    }

    public function updateOrder(Order $order, OrderUpdateDTO $dto): Order
    {
        return DB::transaction(function () use ($order, $dto) {
            $newWarehouseId = $dto->warehouse_id ?? $order->warehouse_id;
            $oldWarehouseId = $order->warehouse_id;
            // Если меняется склад — вернуть товары на старый склад, проверить и списать с нового
            if ($newWarehouseId != $oldWarehouseId) {
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
                foreach ($dto->items as $item) {
                    $stock = Stock::findOrCreate($item->product_id, $newWarehouseId, 0);
                    if (!$stock->hasEnoughStock($item->count)) {
                        throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на новом складе");
                    }
                }
                $order->update(['warehouse_id' => $newWarehouseId]);
            }
            // Обновляем данные покупателя
            if ($dto->customer !== null) {
                $order->update(['customer' => $dto->customer]);
            }
            // Обновляем позиции заказа
            if (!empty($dto->items)) {
                if ($newWarehouseId != $oldWarehouseId) {
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
                        $stock = Stock::findOrCreate($item->product_id, $order->warehouse_id, 0);
                        if (!$stock->hasEnoughStock($item->count)) {
                            throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на складе");
                        }
                    }
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
            return $order->fresh(['warehouse', 'items.product']);
        });
    }

    public function completeOrder(Order $order): Order
    {
        if (!$order->canBeCompleted()) {
            throw new \RuntimeException('Заказ не может быть завершен');
        }

        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return $order->fresh(['warehouse', 'items.product']);
    }

    public function cancelOrder(Order $order): Order
    {
        if (!$order->canBeCanceled()) {
            throw new \RuntimeException('Заказ не может быть отменен');
        }

        return DB::transaction(function () use ($order) {
            // Возвращаем товары на склад
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

            $order->update(['status' => Order::STATUS_CANCELED]);

            return $order->fresh(['warehouse', 'items.product']);
        });
    }

    public function resumeOrder(Order $order): Order
    {
        if (!$order->canBeResumed()) {
            throw new \RuntimeException('Заказ не может быть возобновлен');
        }

        return DB::transaction(function () use ($order) {
            // Проверяем наличие товаров на складе
            foreach ($order->items as $item) {
                $stock = Stock::findOrCreate($item->product_id, $order->warehouse_id, 0);
                
                if (!$stock->hasEnoughStock($item->count)) {
                    throw new \RuntimeException("Недостаточно товара с ID {$item->product_id} на складе для возобновления заказа");
                }
            }

            // Списываем товары со склада
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

            return $order->fresh(['warehouse', 'items.product']);
        });
    }

    public function getOrdersWithFilters(OrderFilterDTO $dto, int $perPage = 15)
    {
        $query = Order::with(['warehouse', 'items.product']);

        if ($dto->status) {
            $query->where('status', $dto->status);
        }
        if ($dto->warehouse_id) {
            $query->where('warehouse_id', $dto->warehouse_id);
        }
        if ($dto->customer) {
            $query->where('customer', 'like', '%' . $dto->customer . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function getActiveOrder(Order $order): Order
    {
        if (!$order->isActive()) {
            throw new \RuntimeException('Можно редактировать только активные заказы');
        }
        
        return $order;
    }

    public function getOrderForEdit(Order $order): Order
    {
        return $this->getActiveOrder($order);
    }
} 