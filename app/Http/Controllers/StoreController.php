<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Menampilkan halaman daftar semua store.
     */
    public function index()
    {
        $stores = Store::all(); // Ambil semua data store dari database
        return view('stores', ['stores' => $stores]);
    }

    /**
     * Menyimpan store yang dipilih ke dalam session.
     */
    public function select(Store $store)
    {
        // Simpan seluruh data store yang dipilih ke dalam session
        session()->put('selected_store', [
            'id' => $store->id,
            'name' => $store->name,
        ]);

        // Arahkan pengguna kembali ke halaman checkout
        return redirect()->route('checkout.index');
    }
}