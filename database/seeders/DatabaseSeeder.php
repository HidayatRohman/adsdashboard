<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ProductSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder untuk user dengan role berbeda
        User::factory()->create([
            'name' => 'Manajer',
            'email' => 'manajer@example.com',
            'role' => 'manajer',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Marketing',
            'email' => 'marketing@example.com',
            'role' => 'marketing',
            'password' => bcrypt('password'),
        ]);

    // sample products
    $this->call(ProductSeeder::class);
    }
}
