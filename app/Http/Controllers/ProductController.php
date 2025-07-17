<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::whereHas('products', function ($query) {
            $query->whereNull('parent_id');
        })->with(['products' => function ($query) {
            $query->whereNull('parent_id');
        }])->get();

        return view('index', [
            'categories' => $categories,
            'selectedStore' => session()->get('selected_store', null)
        ]);
    }

    public function show(Product $product)
    {
        if ($product->parent_id) {
            $product = $product->parent;
        }
        $product->load('variants.optionGroups.options', 'optionGroups.options');
        return view('product-detail', ['product' => $product]);
    }
}