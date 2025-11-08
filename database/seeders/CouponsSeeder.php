<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coupons')->insert([
            ['id' => '1', 'code' => 'DISCOUNT10', 'type' => 'percent', 'value' => 12.00, 'cart_value' => 5000.00, 'expiry_date' => '2024-12-31'],
            ['id' => '2', 'code' => 'FLAT50', 'type' => 'fixed', 'value' => 500.00, 'cart_value' => 2000.00, 'expiry_date' => '2025-11-30'],
            ['id' => '3', 'code' => 'SAVE20', 'type' => 'percent', 'value' => 20.00, 'cart_value' => 10000.00, 'expiry_date' => '2025-12-15'],
            ['id' => '4', 'code' => 'WELCOME5', 'type' => 'fixed', 'value' => 300.00, 'cart_value' => 1500.00, 'expiry_date' => '2026-10-31'],
            ['id' => '5', 'code' => 'HOLIDAY25', 'type' => 'percent', 'value' => 25.00, 'cart_value' => 8000.00, 'expiry_date' => '2026-12-25'],
        ]);
    }
}
