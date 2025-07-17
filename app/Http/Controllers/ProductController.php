<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman utama dengan semua produk yang dikelompokkan per kategori.
     */
    public function index()
    {
        // Ambil semua kategori yang memiliki produk induk (bukan varian),
        // dan muat juga produk-produknya.
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

    /**
     * Menampilkan halaman detail produk yang dinamis.
     */
    public function show(Product $product)
    {
        if ($product->parent_id) {
            $product = $product->parent;
        }
        
        $product->load('variants.optionGroups.options', 'optionGroups.options');

        return view('product-detail', [
            'product' => $product
        ]);
    }
}