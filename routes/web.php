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