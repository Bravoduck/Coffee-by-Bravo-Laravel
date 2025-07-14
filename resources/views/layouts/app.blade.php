<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Coffee by Bravo</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="text/javascript"
      src="https://app.midtrans.com/snap/snap.js"
      data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    @vite(['resources/css/app.css'])
</head>

<body>

    @yield('content')

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
    <div class="modal-overlay-custom" id="confirm-delete-modal">
        <div class="modal-content-custom">
            <h3>Kamu Yakin?</h3>
            <p>Kamu akan membatalkan semua pesanan dan tidak akan melanjutkan pembayaran.</p>
            <div class="modal-actions-custom">
                <button id="confirm-delete-btn" class="modal-btn-secondary">Ya, Batalkan</button>
                <button id="cancel-delete-btn" class="modal-btn-primary">TIDAK JADI</button>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>