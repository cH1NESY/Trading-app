<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function showList(Request $request)
    {
        $products = Product::with(['stocks.warehouse'])->get();
        return view('products.index', compact('products'));
    }
} 