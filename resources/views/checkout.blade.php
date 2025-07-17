@extends('layouts.app')

@section('content')
<div class="mobile-container">
    <header class="detail-header">
        <a href="{{ url('/') }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.82843 10.9999H20V12.9999H7.82843L13.1924 18.3638L11.7782 19.778L4 11.9999L11.7782 4.22168L13.1924 5.63589L7.82843 10.9999Z"></path>
            </svg>
        </a>
        <h1>Checkout</h1>
    </header>

    <main class="checkout-main">
        {{-- Bagian Lokasi Pickup --}}
        <section class="pickup-location-section">
            <h3>Ambil pesananmu di</h3>
            <a href="{{ route('stores.index') }}" class="store-info-link">
                <div class="store-info">
                    <div class="store-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 20H3V11H2V20.5C2 21.3284 2.67157 22 3.5 22H20.5C21.3284 22 22 21.3284 22 20.5V11H21V20ZM12 2C15.3137 2 18 4.68629 18 8C18 12.4183 12 19 12 19C12 19 6 12.4183 6 8C6 4.68629 8.68629 2 12 2ZM12 10.5C13.3807 10.5 14.5 9.38071 14.5 8C14.5 6.61929 13.3807 5.5 12 5.5C10.6193 5.5 9.5 6.61929 9.5 8C9.5 9.38071 10.6193 10.5 12 10.5Z"></path>
                        </svg>
                    </div>
                    @if($selectedStore)
                    <span class="store-name-display">{{ $selectedStore['name'] }}</span>
                    @else
                    <span class="store-name-display text-danger">Pilih Lokasi Pickup</span>
                    @endif
                </div>
            </a>
            <div class="pickup-estimate">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM13 12H17V14H11V7H13V12Z"></path>
                </svg>
                <span>Estimasi pesanan siap diambil 8 - 15 menit</span>
            </div>
        </section>

        {{-- ▼▼▼ BAGIAN NAMA PEMESAN YANG KEMBALI ▼▼▼ --}}
        <section class="customer-details-section">
            <div class="form-group">
                <label for="customer_name">Nama Pemesan</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Masukkan nama Anda" required>
            </div>
        </section>

        {{-- Bagian Detail Pesanan --}}
        <div id="checkout-order-details">
            <section>
                <div class="section-header">
                    <h2>Detail Pesanan</h2>
                    <a href="{{ url('/') }}" class="add-more-btn">Tambah</a>
                </div>

                @if ($cart && count($cart) > 0)
                <div id="cart-items-container">
                    @php $totalPrice = 0; @endphp
                    @foreach ($cart as $id => $details)
                    @php
                    $itemPrice = $details['price'];
                    // Di sini kita akan tambahkan logika kalkulasi harga kustomisasi nanti
                    $totalPrice += $itemPrice * $details['quantity'];
                    @endphp
                    <div class="cart-item" data-id="{{ $id }}">
                        <div class="cart-item-details-column">
                            <h3>{{ $details['name'] }}</h3>
                            @php
                            // 1. Daftar urutan yang benar sesuai halaman detail
                            $optionOrder = [
                            'Large Ice', 'Less Sweet', 'Less Ice', 'More Ice', '+1 Shot', '+2 Shot', 'Oat Milk', 'Almond Milk', 'Aren',
                            'Hazelnut', 'Pandan', 'Manuka', 'Vanilla', 'Salted Caramel',
                            'Caramel Sauce', 'Crumble', 'Milo Powder', 'Oreo Crumbs'
                            ];

                            $customizations = $details['customizations'] ?? [];
                            $sortedCustomizations = [];

                            // 2. Loop melalui urutan yang benar
                            foreach ($optionOrder as $option) {
                            // 3. Jika kustomisasi ada di dalam pesanan, tambahkan ke array yang sudah urut
                            if (in_array($option, $customizations)) {
                            $sortedCustomizations[] = $option;
                            }
                            }
                            @endphp
                            <p>{{ !empty($customizations) ? implode(', ', $customizations) : 'Regular' }}</p>
                            <span class="cart-item-price-main">Rp {{ number_format($itemPrice * $details['quantity'], 0, ',', '.') }}</span>
                        </div>
                        <div class="cart-item-media-column">
                            <img src="{{ asset($details['image']) }}" alt="{{ $details['name'] }}" class="cart-item-image">
                            <div class="cart-item-actions">
                                <button class="edit-item-btn" title="Edit Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15.2929 3.29289C16.0739 2.51184 17.34 2.51184 18.121 3.29289L19.707 4.87868C20.4881 5.65973 20.4881 6.92606 19.707 7.70711L10.4141 17H7V13.5858L15.2929 3.29289ZM16 5.41421L8 13.4142V16H10.5858L18.5858 8.00005L16 5.41421Z"></path>
                                        <path d="M16 5.41421L18.5858 8.00005L17.2929 9.29294L14.7071 6.70716L16 5.41421Z"></path>
                                        <path d="M5 20H19V21C19 21.5523 18.5523 22 18 22H6C5.44772 22 5 21.5523 5 21V20Z"></path>
                                    </svg>
                                </button>
                                <div class="quantity-selector">
                                    @if ($details['quantity'] > 1)
                                    <button class="decrease-item-qty">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M5 11V13H19V11H5Z"></path>
                                        </svg>
                                    </button>
                                    @else
                                    <button class="decrease-item-qty">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z"></path>
                                        </svg>
                                    </button>
                                    @endif
                                    <span>{{ $details['quantity'] }}</span>
                                    <button class="increase-item-qty">+</button>
                                </div>
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
        </div>
    </main>

    @if ($cart && count($cart) > 0)
    <footer class="sticky-footer checkout-footer">
        <div class="checkout-total-summary">
            <span class="total-label">Total Pembayaran</span>
            <span class="total-amount">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
        </div>
        <a href="#" id="process-payment-btn" class="add-to-cart-btn">Bayar</a>
    </footer>
    @endif
</div>
@endsection

<script>
    window.cartItems = @json($cart ?? []);
</script>

@push('scripts')
@vite('resources/js/checkout.js')
@endpush