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
        $minPrice = $request->query('min') ? $request->query('min') : 1;
        $maxPrice = $request->query('max') ? $request->query('max') : 5000;
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
        where(function($query) use($minPrice, $maxPrice){
            $query->whereBetween('regular_price', [$minPrice, $maxPrice])->orWhereBetween('sale_price', [$minPrice, $maxPrice]);
        })->
        orderBy($orderColumn, $orderOrder)->paginate($size);
        return view('shop', compact('products', 'size', 'order', 'brands', 'filterBrands', 'categories', 'filterCategories', 'minPrice', 'maxPrice'));
    }

    /**
     * Display the specified product details and fetch next/previous products.
     *
     * @param string $productSlug
     * @return \Illuminate\View\View
     */
    public function productDetails($productSlug)
    {
        // Finding the current product (use firstOrFail for robust error handling)
        $product = Product::where('slug', $productSlug)->firstOrFail();

        // Finding the previous product (closest ID that is lower than current)
        // This query fixes the 'Undefined variable' error by creating $prevProduct.
        $prevProduct = Product::where('id', '<', $product->id)->orderBy('id', 'desc')->first();

        // Finding the next product (closest ID that is higher than current)
        // This query creates $nextProduct.
        $nextProduct = Product::where('id', '>', $product->id)->orderBy('id', 'asc')->first();
        
        // Fetching related products using original/previous logic
        $related_products = Product::where('slug', '<>', $productSlug)->get()->take(8);

        return view('details', [
            'product' => $product,
            'related_products' => $related_products,
            'prevProduct' => $prevProduct,  
            'nextProduct' => $nextProduct,  
        ]);
    }
}
