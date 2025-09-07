<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $order = $request->query('order') ? $request->query('order') : -1;
        $orderColumn = '';
        $orderOrder = '';
        switch($order)
        {
            case 1:
                $orderColumn = 'sale_price';
                $orderOrder = 'ASC';
                break;
            case 2:
                $orderColumn = 'sale_price';
                $orderOrder = "DESC";
                break;
            case 3:
                $orderColumn = 'created_at';
                $orderOrder = "ASC";
                break;
            case 4:
                $orderColumn = 'created_at';
                $orderOrder = 'DESC';
                break;
            default:
                $orderColumn = 'id';
                $orderOrder = 'DESC';
        }
        $products = Product::orderBy($orderColumn, $orderOrder)->paginate($size);
        return view('shop', compact('products', 'size', 'order'));
    }

    public function productDetails($productSlug)
    {
        $product = Product::where('slug', $productSlug)->first();
        $related_products = Product::where('slug', '<>', $productSlug)->get()->take(8);
        return view('details', compact('product', 'related_products'));
    }
}
