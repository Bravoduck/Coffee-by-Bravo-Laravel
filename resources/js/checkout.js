document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Jika tidak ada kontainer item, berarti kita bukan di halaman checkout. Hentikan script.
    if (!cartItemsContainer) {
        return;
    }

    // Fungsi utama yang "mendengarkan" semua klik di area keranjang
    cartItemsContainer.addEventListener('click', function (event) {
        const target = event.target;
        const increaseBtn = target.closest('.increase-item-qty');
        const decreaseBtn = target.closest('.decrease-item-qty');
        const editBtn = target.closest('.edit-item-btn');

        if (increaseBtn) {
            handleQuantityChange(increaseBtn, 1);
        } else if (decreaseBtn) {
            handleQuantityChange(decreaseBtn, -1);
        } else if (editBtn) {
            // Kita akan implementasikan ini setelah fungsi dasar berjalan
            alert('Fitur Edit akan dibuat selanjutnya!');
        }
    });

    /**
     * Menangani perubahan kuantitas dan mengirim permintaan ke server.
     * @param {HTMLElement} button - Tombol yang diklik (+ atau -).
     * @param {number} change - Perubahan kuantitas (1 atau -1).
     */
    async function handleQuantityChange(button, change) {
        const cartItem = button.closest('.cart-item');
        const itemId = cartItem.dataset.id;
        const quantitySpan = cartItem.querySelector('.quantity-selector span');
        let currentQuantity = parseInt(quantitySpan.textContent, 10);
        const newQuantity = currentQuantity + change;

        // Nonaktifkan semua tombol di item ini untuk mencegah klik ganda
        cartItem.querySelectorAll('button').forEach(btn => btn.disabled = true);

        if (newQuantity < 1) {
            // Jika kuantitas menjadi 0, jalankan fungsi hapus
            if (confirm('Anda yakin ingin menghapus item ini?')) {
                await sendRequest('/checkout/remove', { id: itemId });
            } else {
                // Jika batal, aktifkan kembali tombolnya
                cartItem.querySelectorAll('button').forEach(btn => btn.disabled = false);
            }
        } else {
            // Jika kuantitas valid, jalankan fungsi update
            await sendRequest('/checkout/update', { id: itemId, quantity: newQuantity });
        }
    }

    /**
     * "Mesin" untuk mengirim data ke server via Fetch API.
     * @param {string} url - Alamat URL tujuan di server.
     * @param {object} data - Data yang akan dikirim (e.g., {id: 1, quantity: 2}).
     */
    async function sendRequest(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Terjadi kesalahan pada server.');
            }

            // Jika berhasil, muat ulang halaman untuk menampilkan perubahan.
            // Ini adalah cara paling sederhana dan efektif untuk saat ini.
            window.location.reload();

        } catch (error) {
            console.error('Request Error:', error);
            alert('Gagal memperbarui keranjang: ' + error.message);
            // Jika gagal, muat ulang halaman agar tombol kembali aktif
            window.location.reload();
        }
    }
});