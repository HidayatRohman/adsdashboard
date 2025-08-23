<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Paket A', 'price' => 500000],
            ['name' => 'Paket B', 'price' => 750000],
            ['name' => 'Paket C', 'price' => 1200000],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
