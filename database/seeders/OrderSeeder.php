<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        // Создаем несколько тестовых заказов
        $customers = [
            'Иванов Иван Иванович',
            'Петров Петр Петрович',
            'Сидоров Сидор Сидорович',
            'Козлов Алексей Владимирович',
            'Морозов Дмитрий Сергеевич',
        ];

        $statuses = ['active', 'completed', 'canceled'];

        for ($i = 0; $i < 10; $i++) {
            $warehouse = $warehouses->random();
            $customer = $customers[array_rand($customers)];
            $status = $statuses[array_rand($statuses)];

            $order = Order::create([
                'customer' => $customer,
                'warehouse_id' => $warehouse->id,
                'status' => $status,
                'completed_at' => $status === 'completed' ? now() : null,
            ]);

            // Добавляем от 1 до 3 позиций в заказ
            $orderItemsCount = rand(1, 3);
            $selectedProducts = $products->random($orderItemsCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'count' => $quantity,
                ]);
            }
        }
    }
} 