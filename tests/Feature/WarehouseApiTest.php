<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Warehouse;
use App\Models\Order;

class WarehouseApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_warehouses_with_orders_count()
    {
        $warehouse = Warehouse::factory()->create(['name' => 'Склад 1']);
        // Создаём заказ для склада
        Order::factory()->create(['warehouse_id' => $warehouse->id]);

        $response = $this->getJson('/api/warehouses');
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'orders_count']
                ]
            ])
            ->assertJsonPath('data.0.name', 'Склад 1')
            ->assertJsonPath('data.0.orders_count', 1);
    }
} 