<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman utama dengan semua produk.
     */
    public function index()
    {
        $categories = Category::with('products')->get();
        $cart = session()->get('cart', []);
        $selectedStore = session()->get('selected_store', null);

        return view('index', [
            'categories' => $categories,
            'cart' => $cart,
            'selectedStore' => $selectedStore
            
        ]);
    }

    /**
     * Menampilkan halaman detail untuk satu produk.
     * INI ADALAH METHOD YANG HILANG.
     */
    public function show(Product $product)
    {
        // Laravel akan otomatis menemukan produk berdasarkan slug di URL
        // Lalu kita kirim data produk tersebut ke view 'product-detail'
        return view('product-detail', [
            'product' => $product
        ]);
    }
}