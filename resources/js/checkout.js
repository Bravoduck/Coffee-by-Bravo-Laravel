document.addEventListener('DOMContentLoaded', function () {

    /**
     * Objek utama untuk mengelola semua logika di halaman Checkout.
     */
    const CheckoutPage = {
        // Menyimpan semua elemen UI yang dibutuhkan
        ui: {
            cartItemsContainer: document.getElementById('cart-items-container'),
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            confirmDeleteModal: document.getElementById('confirm-delete-modal'),
            confirmDeleteBtn: document.getElementById('confirm-delete-btn'),
            cancelDeleteBtn: document.getElementById('cancel-delete-btn'),
            processPaymentBtn: document.getElementById('process-payment-btn') // Tombol Lanjutkan
        },

        // Menyimpan state sementara
        state: {
            itemIdToDelete: null
        },

        /**
         * Titik awal untuk menjalankan semua script.
         */
        init() {
            if (!this.ui.cartItemsContainer) {
                return;
            }
            this.registerEventListeners();
            this.registerPaymentListener();
        },

        /**
         * Mendaftarkan semua event listener yang dibutuhkan.
         */
        registerPaymentListener() { // <-- FUNGSI BARU
             if (this.ui.processPaymentBtn) {
                this.ui.processPaymentBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.processPayment(event.currentTarget);
                });
            }
        },

        registerEventListeners() {
            // Listener untuk tombol +/-
            this.ui.cartItemsContainer.addEventListener('click', (event) => {
                const increaseBtn = event.target.closest('.increase-item-qty');
                const decreaseBtn = event.target.closest('.decrease-item-qty');

                if (increaseBtn) this.handleQuantityChange(increaseBtn, 1);
                if (decreaseBtn) this.handleQuantityChange(decreaseBtn, -1);
            });

            // Listener untuk pop-up konfirmasi hapus
            if (this.ui.confirmDeleteModal) {
                this.ui.confirmDeleteBtn.addEventListener('click', () => {
                    if (this.state.itemIdToDelete) {
                        this.sendRequest('/checkout/remove', { id: this.state.itemIdToDelete }, 'menghapus');
                    }
                });

                this.ui.cancelDeleteBtn.addEventListener('click', () => this.hideDeleteModal());

                this.ui.confirmDeleteModal.addEventListener('click', (e) => {
                    if (e.target === this.ui.confirmDeleteModal) this.hideDeleteModal();
                });
            }

            // Listener untuk tombol "Lanjutkan" ke pembayaran
            if (this.ui.processPaymentBtn) {
                this.ui.processPaymentBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.processPayment(event.currentTarget);
                });
            }
        },

        /**
         * Menangani logika saat tombol +/- diklik.
         */
        handleQuantityChange(button, change) {
            const cartItem = button.closest('.cart-item');
            const itemId = cartItem.dataset.id;
            const quantitySpan = cartItem.querySelector('.quantity-selector span');
            const currentQuantity = parseInt(quantitySpan.textContent, 10);
            const newQuantity = currentQuantity + change;

            if (newQuantity < 1) {
                this.state.itemIdToDelete = itemId;
                this.ui.confirmDeleteModal.classList.add('show');
            } else {
                this.sendRequest('/checkout/update', { id: itemId, quantity: newQuantity }, 'memperbarui');
            }
        },

        /**
         * Mengirim data ke server dan memanggil fungsi update tampilan.
         */
        async sendRequest(url, data, actionText) {
            
            this.hideDeleteModal();

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.ui.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Error server.');
                
                this.updateUI(result.cart);

            } catch (error) {
                alert(`Gagal ${actionText} item: ` + error.message);
            }
        },
        
        /**
         * Memproses pembayaran dengan Midtrans.
         */
        async processPayment(button) {
            button.textContent = 'Memproses...';
            button.style.pointerEvents = 'none';

            try {
                const response = await fetch('/checkout/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.ui.csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Gagal memproses pesanan.');

                window.snap.pay(result.snap_token, {
                    onSuccess: function(result){
                      alert("Pembayaran berhasil!");
                      window.location.href = '/'; 
                    },
                    onPending: function(result){
                      alert("Menunggu pembayaran Anda!");
                      window.location.href = '/';
                    },
                    onError: function(result){
                      alert("Pembayaran gagal!");
                      button.textContent = 'Lanjutkan';
                      button.style.pointerEvents = 'auto';
                    },
                    onClose: function(){
                      alert('Anda menutup pop-up pembayaran.');
                      button.textContent = 'Lanjutkan';
                      button.style.pointerEvents = 'auto';
                    }
                });

            } catch (error) {
                alert(error.message);
                button.textContent = 'Lanjutkan';
                button.style.pointerEvents = 'auto';
            }
        },

        /**
         * Memperbarui semua elemen di halaman secara dinamis.
         */
        updateUI(cart) {
            let grandTotal = 0;
            const allItemIdsInCart = Object.keys(cart);

            document.querySelectorAll('.cart-item').forEach(itemElement => {
                const itemId = itemElement.dataset.id;
                if (allItemIdsInCart.includes(itemId)) {
                    const itemData = cart[itemId];
                    const basePrice = window.cartItems[itemId].price;
                    const subtotal = basePrice * itemData.quantity;
                    grandTotal += subtotal;

                    itemElement.querySelector('.quantity-selector span').textContent = itemData.quantity;
                    itemElement.querySelector('.cart-item-price-main').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}`;
                    
                    const decreaseBtn = itemElement.querySelector('.decrease-item-qty');
                    decreaseBtn.innerHTML = (itemData.quantity > 1) 
                        ? '-' 
                        : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8Z"></path></svg>`;
                } else {
                    itemElement.remove();
                }
            });

            const totalAmountSpan = document.querySelector('.checkout-total-summary .total-amount');
            if (totalAmountSpan) {
                totalAmountSpan.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}`;
            }

            if (allItemIdsInCart.length === 0) {
                document.getElementById('checkout-order-details').innerHTML = `
                <section>
                    <div class="section-header">
                        <h2>Detail Pesanan</h2>
                        <a href="/" class="add-more-btn">Tambah</a>
                    </div>
                    <div class="empty-cart-msg" style="padding: 20px 0;">
                        <p>Keranjang Anda sekarang kosong.</p>
                    </div>
                </section>
                `;
                document.querySelector('.sticky-footer.checkout-footer')?.remove();
            }
        },

        /**
         * Menyembunyikan modal dan mereset state.
         */
        hideDeleteModal() {
            this.ui.confirmDeleteModal.classList.remove('show');
            this.state.itemIdToDelete = null;
        }
    };

    // Jalankan aplikasi
    CheckoutPage.init();
});