<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CartController extends Controller
{
    /**
     * Menampilkan halaman checkout dengan data dari session.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $selectedStore = session()->get('selected_store', null);
        return view('checkout', [
            'cart' => $cart,
            'selectedStore' => $selectedStore
        ]);
    }

    /**
     * Menambahkan atau MENGUPDATE item di keranjang.
     * Logika ini sekarang bisa membedakan antara item baru dan item yang diedit.
     */
    public function add(Request $request)
    {
        // Validasi data, termasuk 'old_cart_item_id' yang opsional
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customizations' => 'nullable|array',
            'old_cart_item_id' => 'nullable|string' 
        ]);

        $product = Product::find($request->product_id);
        $cart = session()->get('cart', []);
        $customizations = $request->input('customizations', []);
        sort($customizations);

        // ▼▼▼ LOGIKA PENTING UNTUK MODE EDIT ▼▼▼
        // Jika ada ID item lama yang dikirim (artinya mode edit),
        // hapus dulu item lama tersebut dari keranjang.
        if ($request->has('old_cart_item_id') && isset($cart[$request->old_cart_item_id])) {
            unset($cart[$request->old_cart_item_id]);
        }

        // Buat ID baru berdasarkan kustomisasi yang baru dipilih
        $newCartItemId = $product->id . '-' . md5(implode('-', $customizations));

        // Cek apakah item dengan kustomisasi BARU ini sudah ada di keranjang
        if (isset($cart[$newCartItemId])) {
            // Jika sudah ada, cukup tambahkan kuantitasnya
            $cart[$newCartItemId]['quantity'] += $request->quantity;
        } else {
            // Jika belum ada, tambahkan sebagai item yang benar-benar baru
            $cart[$newCartItemId] = [
                "id" => $newCartItemId, // Simpan ID unik ini
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

        // Karena aksi ini hanya dari halaman detail, kita bisa langsung redirect kembali ke checkout
        return redirect()->route('checkout.index')->with('status', 'Keranjang berhasil diperbarui!');
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
            'cart' => session('cart', [])
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
            'cart' => session('cart', [])
        ]);
    }
}