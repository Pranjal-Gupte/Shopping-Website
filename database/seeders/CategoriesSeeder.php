<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['id' => '1', 'name' => 'Category 1', 'slug' => 'category-1', 'image' => '1751996536.png'],
            ['id' => '2', 'name' => 'Category 2', 'slug' => 'category-2', 'image' => '1751996562.png'],
            ['id' => '3', 'name' => 'Category 3', 'slug' => 'category-3', 'image' => '1751996576.png'],
        ]);
    }
}
