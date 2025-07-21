<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;


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
        // Возвращаем коллекцию ресурсов
        return response()->json(['success' => true, 'data' => ProductResource::collection($products)]);
    }
}
