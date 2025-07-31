<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Category;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand1 = Brand::where('name', 'Brand 1')->first();
        $brand2 = Brand::where('name', 'Brand 2')->first();
        $brand3 = Brand::where('name', 'Brand 3')->first();
        $category1 = Category::where('name', 'Category 1')->first();
        $category2 = Category::where('name', 'Category 2')->first();
        $category3 = Category::where('name', 'Category 3')->first();

        if($brand3 && $category1 && $brand2 && $category2 && $brand1 && $category3) {
            $brand1Id = $brand1->id;
            $brand2Id = $brand2->id;
            $brand3Id = $brand3->id;
            $category1Id = $category1->id;
            $category2Id = $category2->id;
            $category3Id = $category3->id;

            DB::table('products')->insert([
                [
                    'name' => 'Product 1',
                    'slug' => 'product-1',
                    'short_description' => 'Short description for Product 1',
                    'description' => 'This is the Full/Long description for Product 1',
                    'regular_price' => 1000.00,
                    'sale_price' => 800.00,
                    'SKU' => 'JAKT-RED-MED',
                    'stock_status' => 'in_stock',
                    'featured' => true,
                    'quantity' => 100,
                    'image' => '1753963517.png',
                    'images' => json_encode(['1753963519-1.png', '1753963519-2.png', '1753963519-3.png']),
                    'category_id' => $category1Id,
                    'brand_id' => $brand3Id
                ],
                [
                    'name' => 'Product 2',
                    'slug' => 'product-2',
                    'short_description' => 'Short description for Product 2',
                    'description' => 'This is the Full/Long description for Product 2',
                    'regular_price' => 2990.00,
                    'sale_price' => 2450.00,
                    'SKU' => 'SHRT-LVNDR-LRG',
                    'stock_status' => 'in_stock',
                    'featured' => false,
                    'quantity' => 200,
                    'image' => '1753964815.png',
                    'images' => json_encode(['1753964815-1.png']),
                    'category_id' => $category2Id,
                    'brand_id' => $brand2Id
                ],
                [
                    'name' => 'Product 3',
                    'slug' => 'product-3',
                    'short_description' => 'Short description for Product 3',
                    'description' => 'This is the Full/Long description for Product 3',
                    'regular_price' => 1500.00,
                    'sale_price' => 990.00,
                    'SKU' => 'JAKT-GREY-SML',
                    'stock_status' => 'out_of_stock',
                    'featured' => true,
                    'quantity' => 150,
                    'image' => '1753966233.png',
                    'images' => json_encode(['1753966233-1.png']),
                    'category_id' => $category3Id,
                    'brand_id' => $brand1Id
                ]
            ]);
        }
    }
}
