<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Order;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Сидируем склады и товары
        Warehouse::factory()->count(2)->create();
        Product::factory()->count(3)->create();
    }

    /** @test */
    public function it_creates_order_successfully()
    {
        $warehouse = Warehouse::first();
        $product = Product::first();
        // Добавим остаток вручную
        $warehouse->stocks()->create(['product_id' => $product->id, 'stock' => 10]);

        $payload = [
            'customer' => 'Тестовый клиент',
            'warehouse_id' => $warehouse->id,
            'items' => [
                ['product_id' => $product->id, 'count' => 2]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.customer', 'Тестовый клиент');
    }

    /** @test */
    public function it_validates_order_creation()
    {
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonStructure(['errors']);
    }

    /** @test */
    public function it_returns_paginated_orders_list()
    {
        $warehouse = Warehouse::first();
        $product = Product::first();
        $warehouse->stocks()->create(['product_id' => $product->id, 'stock' => 10]);
        // Создаём несколько заказов
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/orders', [
                'customer' => 'Клиент ' . $i,
                'warehouse_id' => $warehouse->id,
                'items' => [
                    ['product_id' => $product->id, 'count' => 1]
                ]
            ]);
        }
        $response = $this->getJson('/api/orders?per_page=2&page=2');
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.current_page', 2)
            ->assertJsonStructure(['data' => ['data', 'current_page', 'per_page', 'total']]);
    }

    /** @test */
    public function it_returns_order_details()
    {
        $warehouse = Warehouse::first();
        $product = Product::first();
        $warehouse->stocks()->create(['product_id' => $product->id, 'stock' => 10]);
        $response = $this->postJson('/api/orders', [
            'customer' => 'Детальный клиент',
            'warehouse_id' => $warehouse->id,
            'items' => [
                ['product_id' => $product->id, 'count' => 1]
            ]
        ]);
        $orderId = $response->json('data.id');
        $response = $this->getJson('/api/orders/' . $orderId);
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.customer', 'Детальный клиент');
    }
} 