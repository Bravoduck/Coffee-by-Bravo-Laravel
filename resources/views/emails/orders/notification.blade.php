<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Baru Diterima</title>
</head>
<body>
    <h2>Pesanan Baru Telah Diterima!</h2>
    <p>Harap segera siapkan pesanan berikut:</p>
    <hr>
    <h3>Detail Pesanan:</h3>
    <ul>
        <li><strong>ID Pesanan:</strong> {{ $order['id'] }}</li>
        <li><strong>Nama Pemesan:</strong> {{ $order['customer_name'] }}</li>
        <li><strong>Lokasi Pickup:</strong> {{ $order['store_name'] }}</li>
        <li><strong>Total Pembayaran:</strong> Rp {{ number_format($order['total_price'], 0, ',', '.') }}</li>
    </ul>

    <h3>Item yang Dipesan:</h3>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kuantitas</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order['items'] as $item)
                <tr>
                    <td>
                        {{ $item['name'] }}
                        @if(!empty($item['customizations']))
                            <br><small>({{ implode(', ', $item['customizations']) }})</small>
                        @endif
                    </td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <p>Terima kasih.</p>
</body>
</html>