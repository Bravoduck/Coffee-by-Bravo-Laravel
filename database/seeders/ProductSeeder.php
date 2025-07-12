<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Baca file JSON
        $json = File::get(database_path('data/products.json'));
        $data = json_decode($json);

        foreach ($data as $categoryData) {
            // 1. Buat Kategori baru
            $category = Category::create([
                'name' => $categoryData->category,
                'slug' => $categoryData->slug,
            ]);

            // 2. Loop setiap item produk di dalam kategori
            foreach ($categoryData->items as $productData) {
                Product::create([
                    'category_id' => $category->id, // Hubungkan ke ID kategori
                    'name' => $productData->name,
                    'slug' => Str::slug($productData->name), // Buat slug dari nama produk
                    'description' => $productData->description,
                    'image' => $productData->image,
                    'price' => $productData->price,
                    'is_sold_out' => $productData->soldOut ?? false,
                ]);
            }
        }
    }
}