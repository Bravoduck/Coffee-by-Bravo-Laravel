<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menampilkan halaman checkout dengan data dari session.
     */
    public function index()
    {
        $cart = session('cart', []);
        $selectedStore = session()->get('selected_store', null); // <-- TAMBAHKAN INI

        return view('checkout', [
            'cart' => $cart,
            'selectedStore' => $selectedStore // <-- TAMBAHKAN INI
        ]);
    }

    /**
     * Menambahkan item baru ke keranjang.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customizations' => 'nullable|array'
        ]);

        $product = Product::find($request->product_id);
        $cart = session()->get('cart', []);
        $customizations = $request->input('customizations', []);
        sort($customizations);

        // ID unik berdasarkan produk & kustomisasinya
        $cartItemId = $product->id . '-' . md5(implode('-', $customizations));

        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            $cart[$cartItemId] = [
                "product_id" => $product->id,
                "name" => $product->name,
                "slug" => $product->slug,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "image" => $product->image,
                "customizations" => $customizations
            ];
        }

        session()->put('cart', $cart);

        $footerHtml = view('partials.cart-footer', ['cart' => $cart])->render();
        return response()->json([
            'message'     => 'Produk berhasil ditambahkan!',
            'footer_html' => $footerHtml
        ]);
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(Request $request)
    {
        $request->validate(['id' => 'required']);
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'message' => 'Item berhasil dihapus.',
            'cart' => session('cart', []) // Kirim kembali data cart terbaru
        ]);
    }

    /**
     * Meng-update kuantitas item di keranjang.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
        
        return response()->json([
            'message' => 'Kuantitas berhasil diperbarui.',
            'cart' => session('cart', []) // Kirim kembali data cart terbaru
        ]);
    }
}