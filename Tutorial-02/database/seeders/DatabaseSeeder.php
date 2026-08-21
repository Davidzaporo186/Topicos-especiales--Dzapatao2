<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = Product::factory(10)->create();

        foreach ($products as $product) {
            Comment::factory(3)->create([
                'product_id' => $product->id,
            ]);
        }
    }
}