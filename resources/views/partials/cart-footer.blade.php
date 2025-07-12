@php
// Ambil data keranjang dari session. Jika tidak ada, gunakan array kosong.
$totalItems = 0;
$totalPrice = 0;

// Hitung total item dan harga jika keranjang tidak kosong
if ($cart) {
foreach ($cart as $id => $details) {
$totalItems += $details['quantity'];
$totalPrice += $details['price'] * $details['quantity'];
}
}
@endphp

{{-- Hanya tampilkan footer jika ada item di keranjang --}}
@if ($totalItems > 0)
<a href="#" class="cart-footer">
    <div class="cart-summary">
        <span>Cek Keranjang ({{ $totalItems }} produk)</span>
    </div>
    <div class="cart-total">
        <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
    </div>
</a>
@endif