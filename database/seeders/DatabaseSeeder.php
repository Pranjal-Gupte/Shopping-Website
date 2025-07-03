<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@aidaily.epizy.com',
            'mobile' => '0987654321',
            'password' => bcrypt('admin@123'),
            'utype' => 'ADM'
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@aidaily.epizy.com',
            'mobile' => '1234567890',
            'password' => bcrypt('user@123'),
            'utype' => 'USR'
        ]);

        $this->call(BrandsSeeder::class);
    }
}
