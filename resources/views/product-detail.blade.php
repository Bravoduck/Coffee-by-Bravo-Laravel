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
            <h2 id="product-detail-name" data-product-id="{{ $product->id }}">{{ $product->name }}</h2>
            <p id="product-detail-description">{{ $product->description }}</p>
            <p id="product-detail-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            <div class="available-options">
                <h3>Pilihan Tersedia</h3>
                <button class="option-chip">Iced</button>
            </div>
        </section>

        <form id="options-form" data-add-to-cart-url="{{ route('cart.add') }}">
            {{-- ... Seluruh section form Anda dari Ukuran Cup sampai Topping ... --}}
            <section class="option-group">
                <div class="option-group-header">
                    <h2>Ukuran Cup</h2>
                    <p>Wajib, Pilih 1</p>
                </div>
                <div class="option-item"><label><span class="option-name">Regular Ice</span><input type="radio"
                        name="cup-size" data-price="0" checked><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">Large Ice <span class="badge">👍 Banyak
                                disukai</span></span><span class="option-price">+Rp 4.500</span><input type="radio"
                        name="cup-size" data-price="4500"><span class="custom-radio"></span></label></div>
            </section>
            
            {{-- ...dan seterusnya untuk semua section form Anda... --}}
            <section class="option-group">
                <div class="option-group-header">
                    <h2>Sweetness</h2>
                    <p>Wajib, Pilih 1</p>
                </div>
                <div class="option-item"><label><span class="option-name">Normal Sweet</span><input type="radio"
                        name="sweetness" data-price="0" checked><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">Less Sweet</span><input type="radio"
                        name="sweetness" data-price="0"><span class="custom-radio"></span></label></div>
            </section>
            <section class="option-group">
                <div class="option-group-header">
                    <h2>Ice Cube</h2>
                    <p>Wajib, Pilih 1</p>
                </div>
                <div class="option-item"><label><span class="option-name">Normal Ice</span><input type="radio"
                        name="ice" data-price="0" checked><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">Less Ice</span><input type="radio"
                        name="ice" data-price="0"><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">More Ice</span><input type="radio"
                        name="ice" data-price="0"><span class="custom-radio"></span></label></div>
            </section>
            <section class="option-group">
                <div class="option-group-header">
                    <h2>Espresso</h2>
                    <p>Wajib, Pilih 1</p>
                </div>
                <div class="option-item"><label><span class="option-name">Normal Shot</span><input type="radio"
                        name="espresso" data-price="0" checked><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">+1 Shot 👍</span><span
                        class="option-price">+Rp 4.500</span><input type="radio" name="espresso"
                        data-price="4500"><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">+2 Shot</span><span
                        class="option-price">+Rp 9.000</span><input type="radio" name="espresso"
                        data-price="9000"><span class="custom-radio"></span></label></div>
            </section>
            <section class="option-group">
                <div class="option-group-header">
                    <h2>Dairy</h2>
                    <p>Wajib, Pilih 1</p>
                </div>
                <div class="option-item"><label><span class="option-name">Milk</span><input type="radio"
                        name="dairy" data-price="0" checked><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">Oat Milk 👍</span><span
                        class="option-price">+Rp 10.000</span><input type="radio" name="dairy"
                        data-price="10000"><span class="custom-radio"></span></label></div>
                <div class="option-item"><label><span class="option-name">Almond Milk</span><span
                        class="option-price">+Rp 10.000</span><input type="radio" name="dairy"
                        data-price="10000"><span class="custom-radio"></span></label></div>
            </section>
            <section class="option-group" id="syrup-group">
                <div class="option-group-header">
                    <h2>Syrup</h2>
                    <p>Opsional, Maks 1</p>
                </div>
                <div class="option-item"><label><span class="option-name">Aren 👍</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="syrup"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Hazelnut</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="syrup"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Pandan</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="syrup"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Manuka</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="syrup"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Vanilla</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="syrup"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Salted Caramel</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="syrup"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
            </section>
            <section class="option-group" id="topping-group">
                <div class="option-group-header">
                    <h2>Topping</h2>
                    <p>Opsional, Maks 2</p>
                </div>
                <div class="option-item"><label><span class="option-name">Caramel Sauce 👍</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="topping"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Crumble 👍</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="topping"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Milo Powder</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="topping"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
                <div class="option-item"><label><span class="option-name">Oreo Crumbs</span><span
                        class="option-price">+Rp 4.500</span><input type="checkbox" name="topping"
                        data-price="4500"><span class="custom-checkbox"></span></label></div>
            </section>
        </form>
    </main>
    <footer class="sticky-footer">
        <div class="quantity-selector"><button id="decrease-qty">-</button><span id="quantity">1</span><button id="increase-qty">+</button></div>
        <button id="add-to-cart-btn" class="add-to-cart-btn">
            <span id="cart-btn-text">Tambah</span><span id="cart-btn-price"></span>
        </button>
    </footer>
</div>
@endsection

@push('scripts')
    @vite('resources/js/detail-page.js')
@endpush