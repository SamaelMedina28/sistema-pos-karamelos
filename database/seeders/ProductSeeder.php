<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Karamelos',
            'image_path' => 'karamelos.jpg',
            'price_for_kg' => 50,
            'stock_quantity' => 10,
        ], [
            'name' => 'Chicles',
            'image_path' => 'chicles.jpg',
            'price_for_kg' => 40,
            'stock_quantity' => 3,
        ], [
            'name' => 'Chicles de menta',
            'image_path' => 'chicles_menta.jpg',
            'price_for_kg' => 35,
            'stock_quantity' => 3,
        ]);
    }
}
