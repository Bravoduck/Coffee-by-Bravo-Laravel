<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route untuk menampilkan halaman utama (daftar produk)
Route::get('/', [ProductController::class, 'index']);
use App\Http\Controllers\CartController;

// Route untuk menampilkan halaman detail satu produk
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');