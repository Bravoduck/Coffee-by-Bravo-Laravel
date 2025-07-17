@extends('layouts.app')

@section('content')
<div class="mobile-container">
    <header class="detail-header">
        <a href="{{ url('/') }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7.82843 10.9999H20V12.9999H7.82843L13.1924 18.3638L11.7782 19.778L4 11.9999L11.7782 4.22168L13.1924 5.63589L7.82843 10.9999Z"></path></svg>
        </a>
        <h1 id="product-name-title-header">{{ $product->name }}</h1>
    </header>

    <main class="detail-main" data-base-price="{{ $product->price }}">
        <section class="product-summary-display">
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" id="product-detail-image">
            <h2 id="product-detail-name" data-product-id="{{ $product->parent_id ?? $product->id }}">{{ $product->name }}</h2>
            <p id="product-detail-description">{{ $product->description }}</p>
            <p id="product-detail-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            
            {{-- Bagian untuk Tombol Varian (Iced/Hot) --}}
            @if($product->variants->isNotEmpty())
                <div class="available-options">
                    <h3>Pilihan Tersedia</h3>
                </div>
                <div class="variant-selector">
                    @foreach($product->variants as $index => $variant)
                        <button type="button" class="option-chip variant-btn {{ $index == 0 ? 'active' : '' }}" data-variant-id="{{ $variant->id }}">
                            {{ $variant->variant_name }}
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        <form id="options-form" action="{{ route('cart.add') }}" method="POST">
            @csrf
            
            {{-- Input ini akan diisi oleh JavaScript dengan ID varian/produk yang akan ditambahkan --}}
            @if($product->variants->isNotEmpty())
                <input type="hidden" name="product_id" id="product_id_input" value="{{ $product->variants->first()->id }}">
            @else
                <input type="hidden" name="product_id" id="product_id_input" value="{{ $product->id }}">
            @endif
            
            {{-- Input untuk kuantitas --}}
            <input type="hidden" name="quantity" id="quantity-input" value="1">

            {{-- Jika produk memiliki varian, loop melalui varian --}}
            @if($product->variants->isNotEmpty())
                @foreach($product->variants as $index => $variant)
                    <div class="variant-options {{ $index == 0 ? '' : 'hidden' }}" data-options-for="{{ $variant->id }}">
                        @foreach ($variant->optionGroups as $group)
                            @include('partials.option-group', ['group' => $group, 'variantName' => $variant->variant_name])
                        @endforeach
                    </div>
                @endforeach
            @else
                {{-- Fallback untuk produk tanpa varian --}}
                @foreach ($product->optionGroups as $group)
                    @include('partials.option-group', ['group' => $group, 'variantName' => 'Iced'])
                @endforeach
            @endif
        </form>
    </main>

    <footer class="sticky-footer">
        <div class="quantity-selector">
            <button type="button" id="decrease-qty">-</button>
            <span id="quantity">1</span>
            <button type="button" id="increase-qty">+</button>
        </div>
        <button id="add-to-cart-btn" class="add-to-cart-btn" form="options-form">
            <span id="cart-btn-text">Tambah</span><span id="cart-btn-price"></span>
        </button>
    </footer>
</div>
@endsection

@push('scripts')
    @vite('resources/js/detail-page.js')
@endpush