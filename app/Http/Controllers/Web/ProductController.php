<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function showList(Request $request)
    {
        $products = Product::with(['stocks.warehouse'])->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        }

        return view('products.index', compact('products'));
    }
} 