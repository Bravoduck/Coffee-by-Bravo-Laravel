<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        return view('checkout', ['cart' => $cart]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $product = Product::find($request->product_id);
        $cart = session()->get('cart', []);
        $cartItemId = $product->id;
        if(isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            $cart[$cartItemId] = [
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "image" => $product->image
            ];
        }
        session()->put('cart', $cart);
        $footerHtml = view('partials.cart-footer', ['cart' => $cart])->render();
        return response()->json([
            'message'     => 'Produk berhasil ditambahkan!',
            'footer_html' => $footerHtml
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate(['id' => 'required']);
        $cart = session()->get('cart', []);
        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }
        return response()->json(['message' => 'Item berhasil dihapus.']);
    }

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
        return response()->json(['message' => 'Kuantitas berhasil diperbarui.']);
    }
}