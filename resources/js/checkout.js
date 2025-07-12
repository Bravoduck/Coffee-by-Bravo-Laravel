document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Elemen-elemen untuk modal konfirmasi hapus
    const confirmDeleteModal = document.getElementById('confirm-delete-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    if (!cartItemsContainer || !confirmDeleteModal) {
        return; // Keluar jika elemen penting tidak ditemukan
    }

    let itemIdToDelete = null; // Variabel untuk menyimpan ID item yang akan dihapus

    // Event listener utama di area keranjang
    cartItemsContainer.addEventListener('click', function (event) {
        const target = event.target;
        const decreaseBtn = target.closest('.decrease-item-qty');

        if (decreaseBtn) {
            handleQuantityChange(decreaseBtn, -1);
        }
        // Listener untuk tombol tambah bisa ditambahkan di sini jika perlu
    });

    async function handleQuantityChange(button, change) {
        const cartItem = button.closest('.cart-item');
        const itemId = cartItem.dataset.id;
        const quantitySpan = cartItem.querySelector('.quantity-selector span');
        let currentQuantity = parseInt(quantitySpan.textContent, 10);
        const newQuantity = currentQuantity + change;

        if (newQuantity < 1) {
            itemIdToDelete = itemId; // Simpan ID item yang akan dihapus
            confirmDeleteModal.classList.add('show'); // Tampilkan modal
        } else {
            // Logika update kuantitas bisa kita tambahkan di sini
            console.log(`Update item ${itemId} to quantity ${newQuantity}`);
        }
    }

    // Listener untuk tombol "Ya, Hapus" di dalam modal
    confirmDeleteBtn.addEventListener('click', async () => {
        if (itemIdToDelete) {
            await sendRemoveRequest('/checkout/remove', { id: itemIdToDelete });
        }
    });

    // Listener untuk tombol "Tidak Jadi"
    cancelDeleteBtn.addEventListener('click', () => {
        confirmDeleteModal.classList.remove('show'); // Sembunyikan modal
        itemIdToDelete = null; // Reset ID
    });
    
    // Listener untuk menutup modal jika klik di luar area konten
    confirmDeleteModal.addEventListener('click', (e) => {
        if (e.target === confirmDeleteModal) {
             confirmDeleteModal.classList.remove('show');
        }
    });

    async function sendRemoveRequest(url, data) {
        confirmDeleteBtn.disabled = true;
        cancelDeleteBtn.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                const result = await response.json().catch(() => ({ message: 'Error server.' }));
                throw new Error(result.message);
            }
            window.location.reload();
        } catch (error) {
            alert('Gagal menghapus item: ' + error.message);
            window.location.reload();
        }
    }
});