document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Elemen-elemen untuk modal konfirmasi hapus
    const confirmDeleteModal = document.getElementById('confirm-delete-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    // Jika elemen-elemen penting tidak ditemukan, hentikan script
    if (!cartItemsContainer || !confirmDeleteModal || !confirmDeleteBtn || !cancelDeleteBtn) {
        return; 
    }

    let itemIdToDelete = null; // Variabel untuk menyimpan ID item yang akan dihapus

    // Fungsi utama yang "mendengarkan" semua klik di area keranjang
    cartItemsContainer.addEventListener('click', function (event) {
        const target = event.target;
        const increaseBtn = target.closest('.increase-item-qty');
        const decreaseBtn = target.closest('.decrease-item-qty');

        if (increaseBtn) {
            handleQuantityChange(increaseBtn, 1);
        } else if (decreaseBtn) {
            handleQuantityChange(decreaseBtn, -1);
        }
    });

    async function handleQuantityChange(button, change) {
        const cartItem = button.closest('.cart-item');
        const itemId = cartItem.dataset.id;
        const quantitySpan = cartItem.querySelector('.quantity-selector span');
        let currentQuantity = parseInt(quantitySpan.textContent, 10);
        const newQuantity = currentQuantity + change;

        if (newQuantity < 1) {
            // Jika kuantitas jadi 0, tampilkan modal konfirmasi
            itemIdToDelete = itemId; // Simpan ID item yang akan dihapus
            confirmDeleteModal.classList.add('show'); // Tampilkan modal
        } else {
            // Jika kuantitas valid, jalankan fungsi update
            await sendRequest('/checkout/update', { id: itemId, quantity: newQuantity });
        }
    }

    // Tambahkan listener untuk tombol "Ya, Hapus" di dalam modal
    confirmDeleteBtn.addEventListener('click', async () => {
        if (itemIdToDelete) {
            await sendRequest('/checkout/remove', { id: itemIdToDelete });
        }
    });

    // Tambahkan listener untuk tombol "Tidak Jadi"
    cancelDeleteBtn.addEventListener('click', () => {
        confirmDeleteModal.classList.remove('show'); // Sembunyikan modal
        itemIdToDelete = null; // Reset ID
    });

    async function sendRequest(url, data) {
        // Nonaktifkan tombol selama proses
        const buttons = document.querySelectorAll('.cart-item button, .modal-actions-custom button');
        buttons.forEach(btn => btn.disabled = true);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                const result = await response.json().catch(() => ({ message: 'Error tidak diketahui.' }));
                throw new Error(result.message);
            }
            window.location.reload();
        } catch (error) {
            console.error('Request Error:', error);
            alert('Gagal memperbarui keranjang: ' + error.message);
            // Aktifkan kembali tombol jika gagal
            buttons.forEach(btn => btn.disabled = false);
        }
    }
});