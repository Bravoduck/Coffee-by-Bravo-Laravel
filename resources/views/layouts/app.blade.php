<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Coffee by Bravo</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">
    <link rel="shortcut icon" href="/favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body>

    {{-- Ini adalah area kosong tempat "Isi Rumah" akan diletakkan --}}
    @yield('content')


    {{-- Ini adalah area untuk JavaScript yang spesifik per halaman --}}
    @stack('scripts')
    <div class="modal-overlay" id="success-modal" style="display: none;">
        <div class="modal-content">
            <h2>Lengkapi Belanjamu</h2>
            <div class="added-item-summary">
                <img id="modal-product-image" src="" alt="Produk" class="summary-image">
                <div class="summary-details">
                    <h3 id="modal-product-name">Nama Produk</h3>
                    <p id="modal-product-customizations">Kustomisasi</p>
                    <p class="summary-success-msg">Berhasil masuk ke keranjang!</p>
                </div>
                <div class="summary-qty" id="modal-product-quantity">1</div>
            </div>
            <div class="modal-actions">
                <a href="#" id="modal-checkout-btn" class="modal-btn primary">Cek Keranjang</a>
                <button class="modal-btn secondary" id="modal-continue-btn">Lanjut Belanja</button>
            </div>
        </div>
    </div>


</body>

</html>