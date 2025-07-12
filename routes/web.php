<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', [ProductController::class, 'index']);

// Halaman detail produk
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Aksi untuk menambah ke keranjang
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

// Route untuk menampilkan halaman keranjang
Route::get('/checkout', [CartController::class, 'index'])->name('checkout.index');

// Aksi untuk MENGHAPUS item dari keranjang
Route::post('/checkout/remove', [CartController::class, 'remove'])->name('checkout.remove');

// Aksi untuk MENG-UPDATE kuantitas item
Route::post('/checkout/update', [CartController::class, 'update'])->name('checkout.update');