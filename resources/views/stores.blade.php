@extends('layouts.app')

@section('content')
<div class="mobile-container">
    <header class="detail-header store-header">
        <div class="header-col left">
            {{-- Tombol kembali akan mengarahkan ke halaman sebelumnya --}}
            <a href="{{ url()->previous() }}" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7.82843 10.9999H20V12.9999H7.82843L13.1924 18.3638L11.7782 19.778L4 11.9999L11.7782 4.22168L13.1924 5.63589L7.82843 10.9999Z"></path></svg>
            </a>
        </div>
        <div class="header-col center">
            <h1>Store</h1>
        </div>
        <div class="header-col right"></div>
    </header>
    <main class="store-list-main">
        <p class="store-count">{{ count($stores) }} Store tersedia</p>
        <div class="store-list">
            @foreach ($stores as $store)
                <a href="{{ route('stores.select', $store->id) }}" class="store-card">
                    <div class="store-details">
                        <h3 class="store-name">{{ $store->name }}</h3>
                        <p class="store-address">{{ $store->address }}</p>
                        <div class="store-hours open">Buka {{ $store->hours }}</div>
                    </div>
                    <div class="store-amenities">
                        <svg class="go-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"></path></svg>
                    </div>
                </a>
            @endforeach
        </div>
    </main>
</div>
@endsection