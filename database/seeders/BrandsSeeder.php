<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            ['id' => '1', 'name' => 'Brand 1', 'slug' => 'brand-1', 'image' => '1751545522.png'],
            ['id' => '2', 'name' => 'Brand 2', 'slug' => 'brand-2', 'image' => '1751545635.png'],
            ['id' => '3', 'name' => 'Brand 3', 'slug' => 'brand-3', 'image' => '1751545646.png'],
        ]);
    }
}
