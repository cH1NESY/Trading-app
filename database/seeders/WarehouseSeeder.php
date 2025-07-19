<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Склад №1 - Центральный'],
            ['name' => 'Склад №2 - Северный'],
            ['name' => 'Склад №3 - Южный'],
            ['name' => 'Склад №4 - Восточный'],
            ['name' => 'Склад №5 - Западный'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
} 