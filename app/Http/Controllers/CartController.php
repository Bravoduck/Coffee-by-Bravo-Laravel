<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Option; // PENTING: Tambahkan ini untuk mengakses data harga opsi
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
     * Logika ini sekarang bisa menghitung harga kustomisasi dan menangani mode edit.
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

        $allCustomizations = [];
        if ($request->has('customizations')) {
            foreach ($request->customizations as $group) {
                if (is_array($group)) {
                    $allCustomizations = array_merge($allCustomizations, $group);
                } else {
                    $allCustomizations[] = $group;
                }
            }
        }
        sort($allCustomizations);

        $optionsPrice = Option::whereIn('name', $allCustomizations)->sum('price');
        $totalItemPrice = $product->price + $optionsPrice;

        $newCartItemId = $product->id . '-' . md5(implode('-', $allCustomizations));

        if ($request->has('old_cart_item_id') && isset($cart[$request->old_cart_item_id])) {
            unset($cart[$request->old_cart_item_id]);
        }

        if (isset($cart[$newCartItemId])) {
            $cart[$newCartItemId]['quantity'] += $request->quantity;
        } else {
            $cart[$newCartItemId] = [
                "id" => $newCartItemId,
                "product_id" => $product->id,
                "name" => $product->name,
                "slug" => $product->slug,
                "quantity" => (int)$request->quantity,
                "price" => $totalItemPrice,
                "image" => $product->image,
                "customizations" => $allCustomizations
            ];
        }

        session()->put('cart', $cart);

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
