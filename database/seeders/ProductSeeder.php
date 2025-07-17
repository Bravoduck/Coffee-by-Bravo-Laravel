<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\OptionGroup;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(database_path('data/products.json'));
        $data = json_decode($json);

        foreach ($data as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData->slug],
                ['name' => $categoryData->category]
            );

            foreach ($categoryData->items as $productData) {
                // Buat produk induk terlebih dahulu
                $parentProduct = Product::create([
                    'category_id' => $category->id,
                    'name' => $productData->name,
                    'slug' => Str::slug($productData->name),
                    'description' => $productData->description,
                    'image' => $productData->image,
                    'price' => $productData->price,
                    'is_sold_out' => $productData->soldOut ?? false,
                ]);

                // Cek jika produk ini punya varian (Iced/Hot)
                if (isset($productData->variants) && is_array($productData->variants)) {
                    foreach ($productData->variants as $variant) {
                        $childProduct = Product::create([
                            'category_id' => $category->id,
                            'parent_id' => $parentProduct->id,
                            'name' => $productData->name,
                            'variant_name' => $variant->name,
                            'slug' => Str::slug($productData->name . ' ' . $variant->name),
                            'description' => $productData->description,
                            'image' => $productData->image,
                            'price' => $productData->price,
                            'is_sold_out' => $productData->soldOut ?? false,
                        ]);
                        
                        $groupIds = OptionGroup::whereIn('name', $variant->option_groups)->pluck('id');
                        $childProduct->optionGroups()->sync($groupIds);
                    }
                } 
                // Jika produk tidak punya varian, hubungkan langsung
                elseif (isset($productData->option_groups)) {
                    $groupIds = OptionGroup::whereIn('name', $productData->option_groups)->pluck('id');
                    $parentProduct->optionGroups()->sync($groupIds);
                }
            }
        }
    }
}