<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Ноутбук Dell XPS 13', 'price' => 89999.99],
            ['name' => 'Смартфон iPhone 15 Pro', 'price' => 129999.99],
            ['name' => 'Планшет iPad Air', 'price' => 69999.99],
            ['name' => 'Наушники Sony WH-1000XM5', 'price' => 34999.99],
            ['name' => 'Монитор Samsung 27" 4K', 'price' => 45999.99],
            ['name' => 'Клавиатура Logitech MX Keys', 'price' => 12999.99],
            ['name' => 'Мышь Logitech MX Master 3', 'price' => 8999.99],
            ['name' => 'Принтер HP LaserJet Pro', 'price' => 25999.99],
            ['name' => 'Внешний SSD Samsung 1TB', 'price' => 8999.99],
            ['name' => 'Веб-камера Logitech C920', 'price' => 5999.99],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 