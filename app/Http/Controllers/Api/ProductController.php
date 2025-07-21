<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;


class ProductController extends Controller
{
    /**
     * Получить список продуктов с остатками по складам
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Получаем все продукты с остатками по складам
        $products = Product::with(['stocks.warehouse'])->get();
        // Формируем результат: по каждому продукту список складов и остатков
        $result = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'warehouses' => $product->stocks->map(function ($stock) {
                    return [
                        'warehouse_id' => $stock->warehouse->id,
                        'warehouse_name' => $stock->warehouse->name,
                        'stock' => $stock->stock
                    ];
                })
            ];
        });
        return response()->json(['success' => true, 'data' => $result]);
    }
}
