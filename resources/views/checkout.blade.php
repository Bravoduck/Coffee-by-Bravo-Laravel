@extends('layouts.app')

@section('content')
<div class="mobile-container">
    <header class="detail-header">
        <a href="{{ url('/') }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7.82843 10.9999H20V12.9999H7.82843L13.1924 18.3638L11.7782 19.778L4 11.9999L11.7782 4.22168L13.1924 5.63589L7.82843 10.9999Z"></path></svg>
        </a>
        <h1>Checkout</h1>
    </header>

    <main class="checkout-main">
        {{-- Bagian Lokasi Pickup --}}
        <section class="pickup-location-section">
            <h3>Ambil pesananmu di</h3>
            <div class="store-info">
                <div class="store-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C15.3137 2 18 4.68629 18 8C18 12.4183 12 19 12 19C12 19 6 12.4183 6 8C6 4.68629 8.68629 2 12 2ZM12 10.5C13.3807 10.5 14.5 9.38071 14.5 8C14.5 6.61929 13.3807 5.5 12 5.5C10.6193 5.5 9.5 6.61929 9.5 8C9.5 9.38071 10.6193 10.5 12 10.5Z"></path></svg>
                </div>
                {{-- Kita akan buat ini dinamis nanti --}}
                <span class="store-name-display text-danger">Pilih Lokasi Pickup</span> 
            </div>
            <div class="pickup-estimate">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM13 12H17V14H11V7H13V12Z"></path></svg>
                <span>Estimasi pesanan siap diambil 8 - 15 menit</span>
            </div>
        </section>

        {{-- Bagian Detail Pesanan --}}
        <section>
            <div class="section-header">
                <h2>Detail Pesanan</h2>
                <a href="{{ url('/') }}" class="add-more-btn">Tambah</a>
            </div>

            @if ($cart && count($cart) > 0)
                <div id="cart-items-container">
                    @php $totalPrice = 0; @endphp
                    @foreach ($cart as $id => $details)
                        @php $totalPrice += $details['price'] * $details['quantity']; @endphp
                        <div class="cart-item" data-id="{{ $id }}">
                            <div class="cart-item-row-1">
                                <img src="{{ asset($details['image']) }}" alt="{{ $details['name'] }}" class="cart-item-image">
                                <div class="cart-item-details">
                                    <h3>{{ $details['name'] }}</h3>
                                    <p>Regular</p> {{-- Kustomisasi nanti --}}
                                </div>
                                <span class="cart-item-price">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</span>
                            </div>
                            <div class="cart-item-row-2">
                                <button class="edit-item-btn" title="Edit Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                                </button>
                                <div class="quantity-selector">
                                    @if ($details['quantity'] > 1)
                                        <button class="decrease-item-qty">-</button>
                                    @else
                                        {{-- Jika kuantitas 1, tombol kurang menjadi tong sampah --}}
                                        <button class="decrease-item-qty">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8Z"></path></svg>
                                        </button>
                                    @endif
                                    <span>{{ $details['quantity'] }}</span>
                                    <button class="increase-item-qty">+</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-cart-msg" style="padding: 20px 0;">
                    <p>Keranjang Anda masih kosong.</p>
                </div>
            @endif
        </section>
    </main>

    {{-- Footer Pembayaran --}}
    @if ($cart && count($cart) > 0)
        <footer class="sticky-footer checkout-footer">
            <div class="checkout-total-summary">
                <span class="total-label">Total Pembayaran</span>
                <span class="total-amount">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
            </div>
            <a href="#" class="add-to-cart-btn">Lanjutkan</a>
        </footer>
    @endif
</div>
@endsection