<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', [ProductController::class, 'index']);

// Halaman detail produk
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Halaman Checkout
Route::get('/checkout', [CartController::class, 'index'])->name('checkout.index');

// Aksi untuk keranjang
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/checkout/remove', [CartController::class, 'remove'])->name('checkout.remove');
Route::post('/checkout/update', [CartController::class, 'update'])->name('checkout.update');

Route::get('/cart/clear', function () {
    session()->forget('cart'); // Menghapus data keranjang dari session
    return redirect('/checkout')->with('status', 'Keranjang berhasil dikosongkan!');
});