<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menambahkan produk ke dalam keranjang belanja (session).
     */
    public function add(Request $request)
    {
        // 1. Validasi data yang dikirim dari JavaScript
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        // 2. Ambil keranjang yang sudah ada dari session, atau buat array kosong
        $cart = session()->get('cart', []);

        // 3. Buat ID unik untuk item di keranjang (untuk sekarang kita pakai ID produk)
        // Nanti bisa dikembangkan dengan kustomisasi
        $cartItemId = $product->id;

        // 4. Cek apakah produk sudah ada di keranjang
        if (isset($cart[$cartItemId])) {
            // Jika ada, tambahkan kuantitasnya
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            $cart[$cartItemId] = [
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "image" => $product->image
                // Anda bisa tambahkan 'customizations' di sini jika perlu
            ];
        }

        // 5. Simpan kembali data keranjang yang baru ke dalam session
        session()->put('cart', $cart);

        // 6. Render komponen footer dengan data cart yang baru
        $footerHtml = view('partials.cart-footer', ['cart' => $cart])->render();

        // 7. Kirim respons berhasil beserta HTML footer yang baru
        return response()->json([
            'message'     => 'Produk berhasil ditambahkan!',
            'footer_html' => $footerHtml
        ]);
    }
}
