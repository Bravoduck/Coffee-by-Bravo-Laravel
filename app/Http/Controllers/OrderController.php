<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store; // PENTING: Tambahkan ini untuk mengakses data toko
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderNotification;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class OrderController extends Controller
{
    /**
     * Memproses pesanan, menyimpannya ke database, dan mendapatkan token Midtrans.
     */
    public function process(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'selected_store' => 'required|array', // Validasi untuk selected_store
        ]);
        $cart = session()->get('cart', []);
        $selectedStore = session()->get('selected_store');
        $customerName = $request->input('customer_name');

        if (empty($cart) || !$selectedStore) {
            return response()->json(['error' => 'Data tidak lengkap.'], 400);
        }

        // 2. Hitung total harga di server dan siapkan detail item untuk Midtrans
        $totalPrice = 0;
        $item_details = []; // Siapkan array untuk item detail
        foreach ($cart as $id => $details) {
            $totalPrice += $details['price'] * $details['quantity'];

            // Gabungkan nama produk dengan kustomisasinya untuk ditampilkan di Midtrans
            $itemName = $details['name'];
            if (!empty($details['customizations'])) {
                $customText = implode(', ', $details['customizations']);
                $itemName .= ' (' . $customText . ')';
            }
            $itemName = substr($itemName, 0, 50); // Batas Midtrans


            $item_details[] = [
                'id'       => $id,
                'name'     => $itemName,
                'price'    => (int) $details['price'],
                'quantity' => (int) $details['quantity']
            ];
        }

        // 3. Simpan pesanan ke database dengan status 'pending'
        $order = Order::create([
            'order_id'      => 'CBB-' . uniqid(),
            'store_id'      => $selectedStore['id'],
            'customer_name' => $customerName,
            'total_price'   => $totalPrice,
            'status'        => 'pending',
        ]);

        // 4. Simpan setiap item di keranjang ke tabel 'order_items'
        foreach ($cart as $id => $details) {
            $order->items()->create([
                'product_id'    => $details['product_id'],
                'quantity'      => $details['quantity'],
                'price'         => $details['price'],
                'customizations' => $details['customizations'],
            ]);
        }

        // 5. Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // 6. Siapkan semua parameter untuk dikirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'address' => $selectedStore['name'],
            ],
            'item_details' => $item_details,
        ];

        try {
            // 7. Dapatkan Snap Token dan kirim ke frontend
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menerima notifikasi dari Midtrans (Webhook).
     */
    public function webhook(Request $request)
    {
        // Konfigurasi server key
        Config::$serverKey = config('services.midtrans.server_key');

        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;

            // Cari pesanan di database beserta relasinya
            $order = Order::with('items.product.parent', 'store')->where('order_id', $orderId)->first();

            // Jika pembayaran berhasil (settlement atau capture)
            if (($transactionStatus == 'settlement' || $transactionStatus == 'capture') && $order && $order->status == 'pending') {
                // Update status pesanan menjadi 'success'
                $order->update(['status' => 'success']);

                // Kirim email notifikasi dengan data pesanan yang lengkap
                Mail::to('admin@bravoduck.store')->send(new NewOrderNotification($order));

                // Kosongkan keranjang setelah pembayaran berhasil
                session()->forget('cart');
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            // Catat error ke log untuk debugging
            \Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook Error'], 500);
        }
    }
}
