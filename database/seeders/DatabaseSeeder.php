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
        // Seeder untuk user dengan role berbeda
        User::factory()->create([
            'name' => 'Manajer',
            'email' => 'manajer@example.com',
            'role' => 'manajer',
        ]);

        User::factory()->create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
        ]);

        User::factory()->create([
            'name' => 'Marketing',
            'email' => 'marketing@example.com',
            'role' => 'marketing',
        ]);
    }
}
