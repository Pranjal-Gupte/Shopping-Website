<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $order = $request->query('order') ? $request->query('order') : -1;
        $orderColumn = '';
        $orderOrder = '';
        $filterBrands = $request->query('brands');
        $filterCategories = $request->query('categories');
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
        $brands = Brand::orderBy('name',  'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where(function($query) use($filterBrands){
            $query->whereIn('brand_id', explode(',', $filterBrands))->orWhereRaw("'".$filterBrands."'=''");
        })->
        where(function($query) use($filterCategories){
            $query->whereIn('category_id', explode(',', $filterCategories))->orWhereRaw("'".$filterCategories."'=''");
        })->
        orderBy($orderColumn, $orderOrder)->paginate($size);
        return view('shop', compact('products', 'size', 'order', 'brands', 'filterBrands', 'categories', 'filterCategories'));
    }

    public function productDetails($productSlug)
    {
        $product = Product::where('slug', $productSlug)->first();
        $related_products = Product::where('slug', '<>', $productSlug)->get()->take(8);
        return view('details', compact('product', 'related_products'));
    }
}
