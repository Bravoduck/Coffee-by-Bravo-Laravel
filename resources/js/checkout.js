document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Kita akan memakai ulang modal yang sudah ada di halaman detail
    const modal = document.getElementById('success-modal');

    // Jika elemen-elemen penting ini tidak ada, hentikan script
    if (!cartItemsContainer || !modal) {
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

    /**
     * Menangani perubahan kuantitas atau menampilkan pop-up konfirmasi.
     */
    async function handleQuantityChange(button, change) {
        const cartItem = button.closest('.cart-item');
        const itemId = cartItem.dataset.id;
        const quantitySpan = cartItem.querySelector('.quantity-selector span');
        let currentQuantity = parseInt(quantitySpan.textContent, 10);
        const newQuantity = currentQuantity + change;

        if (newQuantity < 1) {
            // Jika kuantitas jadi 0, panggil fungsi untuk menampilkan pop-up konfirmasi
            showDeleteConfirmation(itemId);
        } else {
            // Jika kuantitas valid, jalankan fungsi update
            await sendRequest('/checkout/update', { id: itemId, quantity: newQuantity });
        }
    }
    
    /**
     * Fungsi untuk memodifikasi dan menampilkan modal yang sudah ada
     * menjadi pop-up konfirmasi hapus.
     */
    function showDeleteConfirmation(itemId) {
        itemIdToDelete = itemId; // Simpan ID item

        // Ambil semua elemen di dalam modal
        const modalContent = modal.querySelector('.modal-content');
        const modalTitle = modal.querySelector('h2');
        const modalSummary = modal.querySelector('.added-item-summary');
        const modalActions = modal.querySelector('.modal-actions');

        // 1. Ubah konten modal menjadi mode konfirmasi
        modalTitle.textContent = 'Kamu Yakin?';
        modalTitle.style.color = '#333'; // Warna teks standar

        // Sembunyikan ringkasan produk
        modalSummary.style.display = 'none';

        // Buat dan sisipkan paragraf deskripsi jika belum ada
        let description = modalContent.querySelector('.modal-delete-description');
        if (!description) {
            description = document.createElement('p');
            description.className = 'modal-delete-description';
            modalTitle.after(description);
        }
        description.textContent = 'Kamu akan menghapus item ini dari keranjang.';
        description.style.color = '#757575';
        description.style.marginBottom = '24px';

        // 2. Ubah tombol-tombolnya
        modalActions.innerHTML = `
            <button class="modal-btn secondary" id="cancel-delete-btn-new">TIDAK JADI</button>
            <button class="modal-btn primary" id="confirm-delete-btn-new">YA, BATALKAN</button>
        `;

        // 3. Tambahkan fungsi baru untuk tombol-tombol tersebut
        modal.querySelector('#confirm-delete-btn-new').onclick = async () => {
            await sendRequest('/checkout/remove', { id: itemIdToDelete });
        };
        modal.querySelector('#cancel-delete-btn-new').onclick = () => {
            modal.style.display = 'none';
        };

        // 4. Tampilkan modal yang sudah dimodifikasi
        modal.style.display = 'flex';
    }

    /**
     * Fungsi untuk mengirim permintaan ke server.
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
            if (!response.ok) {
                const result = await response.json().catch(() => ({ message: 'Error server.' }));
                throw new Error(result.message);
            }
            window.location.reload();
        } catch (error) {
            alert('Gagal memperbarui keranjang: ' + error.message);
            window.location.reload();
        }
    }
});