<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Warehouse;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_products_with_warehouses_and_stocks()
    {
        $warehouse = Warehouse::factory()->create(['name' => 'Склад 1']);
        $product = Product::factory()->create(['name' => 'Товар 1', 'price' => 100]);
        $warehouse->stocks()->create(['product_id' => $product->id, 'stock' => 5]);

        $response = $this->getJson('/api/products');
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'price', 'warehouses']
                ]
            ])
            ->assertJsonPath('data.0.warehouses.0.warehouse_name', 'Склад 1')
            ->assertJsonPath('data.0.warehouses.0.stock', 5);
    }
} 