<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function process(Request $request)
    {
        // 1. Ambil data keranjang dari session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Keranjang Anda kosong!'], 400);
        }

        // 2. Konfigurasi Midtrans dari file .env
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // 3. Hitung total harga di server
        $gross_amount = 0;
        $order_items = [];
        foreach ($cart as $id => $details) {
            $gross_amount += $details['price'] * $details['quantity'];
            $order_items[] = [
                'id'       => $id,
                'price'    => (int) $details['price'],
                'quantity' => (int) $details['quantity'],
                'name'     => $details['name']
            ];
        }

        // 4. Siapkan detail transaksi untuk dikirim ke Midtrans
        $transaction_details = [
            'order_id'     => 'CBB-' . uniqid(), // ID Pesanan Unik
            'gross_amount' => (int) $gross_amount,
        ];
        
        // Data pelanggan (bisa diisi dengan data user jika sudah login)
        $customer_details = [
            'first_name' => "Bravoduck", // Contoh
            'email'      => "customer@example.com",
            'phone'      => "081234567890",
        ];

        // Gabungkan semua parameter
        $params = [
            'transaction_details' => $transaction_details,
            'customer_details'    => $customer_details,
            'item_details'        => $order_items,
        ];

        try {
            // 5. Minta token pembayaran ke Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // 6. Kirim token kembali ke frontend
            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}