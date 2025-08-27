<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(12);
        return view('shop', compact('products'));
    }

    public function productDetails($productSlug)
    {
        $product = Product::where('slug', $productSlug)->first();
        $related_products = Product::where('slug', '<>', $productSlug)->get()->take(8);
        return view('details', compact('product', 'related_products'));
    }
}
