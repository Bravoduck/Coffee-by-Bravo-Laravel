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
                            <div class="cart-item-details-column">
                                <h3>{{ $details['name'] }}</h3>
                                <p>Regular</p>
                                <span class="cart-item-price-main">Rp {{ number_format($details['price'], 0, ',', '.') }}</span>
                            </div>
                            <div class="cart-item-media-column">
                                <img src="{{ asset($details['image']) }}" alt="{{ $details['name'] }}" class="cart-item-image">
                                <div class="cart-item-actions">
                                    <button class="edit-item-btn" title="Edit Item">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M15.2929 3.29289C16.0739 2.51184 17.34 2.51184 18.121 3.29289L19.707 4.87868C20.4881 5.65973 20.4881 6.92606 19.707 7.70711L10.4141 17H7V13.5858L15.2929 3.29289ZM16 5.41421L8 13.4142V16H10.5858L18.5858 8.00005L16 5.41421Z"></path><path d="M16 5.41421L18.5858 8.00005L17.2929 9.29294L14.7071 6.70716L16 5.41421Z"></path><path d="M5 20H19V21C19 21.5523 18.5523 22 18 22H6C5.44772 22 5 21.5523 5 21V20Z"></path></svg>
                                    </button>
                                    <div class="quantity-selector">
                                        @if ($details['quantity'] > 1)
                                            <button class="decrease-item-qty">-</button>
                                        @else
                                            <button class="decrease-item-qty">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 11V13H19V11H5Z"></path></svg>
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
    </main>

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

@push('scripts')
    @vite('resources/js/checkout.js')
@endpush