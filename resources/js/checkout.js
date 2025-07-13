document.addEventListener('DOMContentLoaded', function () {
    // === ELEMEN-ELEMEN PENTING ===
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const confirmDeleteModal = document.getElementById('confirm-delete-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    // Jika elemen penting tidak ada, hentikan script
    if (!cartItemsContainer || !confirmDeleteModal || !confirmDeleteBtn || !cancelDeleteBtn) {
        return;
    }

    let itemIdToDelete = null; // Variabel untuk menyimpan ID item yang akan dihapus

    // === EVENT LISTENER UTAMA ===
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

    // Menambahkan listener untuk tombol di dalam pop-up
    confirmDeleteBtn.addEventListener('click', () => {
        if (itemIdToDelete) {
            sendRequest('/checkout/remove', {
                id: itemIdToDelete
            }, 'menghapus');
        }
    });

    cancelDeleteBtn.addEventListener('click', () => {
        confirmDeleteModal.classList.remove('show');
        itemIdToDelete = null;
    });

    confirmDeleteModal.addEventListener('click', (e) => {
        if (e.target === confirmDeleteModal) {
            confirmDeleteModal.classList.remove('show');
        }
    });

    // === LOGIKA INTI ===

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
            itemIdToDelete = itemId;
            confirmDeleteModal.classList.add('show');
        } else {
            await sendRequest('/checkout/update', {
                id: itemId,
                quantity: newQuantity
            }, 'memperbarui');
        }
    }

    /**
     * "Mesin" utama untuk mengirim permintaan ke server.
     */
    async function sendRequest(url, data, actionText) {
        // Nonaktifkan semua tombol di halaman untuk mencegah klik ganda
        const buttons = document.querySelectorAll('.cart-item button, .modal-actions-custom button');
        buttons.forEach(btn => btn.disabled = true);
        
        // Redupkan item yang sedang diproses jika ada
        const itemElement = document.querySelector(`.cart-item[data-id="${data.id}"]`);
        if(itemElement) itemElement.style.opacity = '0.5';

        // Sembunyikan modal jika sedang terbuka
        if(confirmDeleteModal) confirmDeleteModal.classList.remove('show');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json(); // Pindahkan ini ke atas
            if (!response.ok) {
                throw new Error(result.message || 'Error server.');
            }
            
            // Hapus reload() dan panggil fungsi updateUI
            updateUI(result.cart); 

        } catch (error) {
            alert(`Gagal ${actionText} item: ` + error.message);
        } finally {
            // Aktifkan kembali semua tombol setelah selesai
            buttons.forEach(btn => btn.disabled = false);
            if(itemElement) itemElement.style.opacity = '1';
        }
    }

    /**
     * Fungsi untuk memperbarui tampilan (UI) tanpa refresh.
     */
    function updateUI(cart) {
        let grandTotal = 0;
        const allItemIdsInCart = Object.keys(cart);

        document.querySelectorAll('.cart-item').forEach(itemElement => {
            const itemId = itemElement.dataset.id;

            if (allItemIdsInCart.includes(itemId)) {
                // Jika item masih ada di keranjang, update datanya
                const itemData = cart[itemId];
                const quantitySpan = itemElement.querySelector('.quantity-selector span');
                const priceSpan = itemElement.querySelector('.cart-item-price-main');
                const decreaseBtn = itemElement.querySelector('.decrease-item-qty');

                const itemBasePrice = window.cartItems[itemId].price;
                const itemSubtotal = itemBasePrice * itemData.quantity;
                grandTotal += itemSubtotal;

                quantitySpan.textContent = itemData.quantity;
                priceSpan.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(itemSubtotal)}`;

                if (itemData.quantity > 1) {
                    decreaseBtn.innerHTML = `-`;
                } else {
                    decreaseBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8Z"></path></svg>`;
                }

            } else {
                // Jika item sudah tidak ada (dihapus), hilangkan dari tampilan
                itemElement.remove();
            }
        });

        // Update Grand Total di footer
        const totalAmountSpan = document.querySelector('.checkout-total-summary .total-amount');
        if (totalAmountSpan) {
            totalAmountSpan.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}`;
        }

        // Jika keranjang jadi kosong, tampilkan pesan dan hapus footer
        if (allItemIdsInCart.length === 0) {
            document.querySelector('.checkout-main').innerHTML = `<div class="empty-cart-msg" style="padding: 20px 0;"><p>Keranjang Anda sekarang kosong.</p></div>`;
            document.querySelector('.sticky-footer.checkout-footer') ? .remove();
        }
    }
});