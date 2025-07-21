<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;

class StockMovementApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_stock_movements_with_filters_and_pagination()
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        // Создаём несколько движений
        for ($i = 1; $i <= 3; $i++) {
            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'quantity' => $i,
                'type' => 'test',
                'description' => 'Движение ' . $i,
            ]);
        }
        $response = $this->getJson('/api/stock-movements?per_page=2&page=2&product_id=' . $product->id);
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'data', 'current_page', 'per_page', 'total'
                ]
            ])
            ->assertJsonPath('data.current_page', 2)
            ->assertJsonPath('data.data.0.description', 'Движение 3');
    }
} 