/**
 * @file detail-page.js
 * FINAL VERSION
 */
document.addEventListener('DOMContentLoaded', () => {
    const detailMain = document.querySelector('.detail-main');
    if (!detailMain) return;

    const basePrice = parseInt(detailMain.dataset.basePrice, 10);
    let currentPrice = 0;
    const form = document.getElementById('options-form');
    const quantityElement = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    const successModal = document.getElementById('success-modal');
    const modalContinueBtn = document.getElementById('modal-continue-btn');
    const modalCheckoutBtn = document.getElementById('modal-checkout-btn'); // Tombol baru

    function calculateTotalPrice() {
        let optionsPrice = 0;
        form.querySelectorAll('input:checked').forEach(option => {
            optionsPrice += parseInt(option.dataset.price, 10) || 0;
        });
        const quantity = parseInt(quantityElement.textContent, 10);
        currentPrice = (basePrice + optionsPrice) * quantity;
        updateButtonPrice(currentPrice);
    }

    function updateButtonPrice(price) {
        const formattedPrice = new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(price);
        addToCartBtn.textContent = `Tambah • ${formattedPrice.replace('Rp', 'Rp ')}`;
    }

    function showSuccessModal(productData) {
        const defaultOptions = ['Regular Ice', 'Normal Sweet', 'Normal Ice', 'Normal Shot', 'Milk'];
        const displayedCustomizations = productData.customizations.filter(c => !defaultOptions.includes(c));

        document.getElementById('modal-product-image').src = document.getElementById('product-detail-image').src;
        document.getElementById('modal-product-name').textContent = `Iced ${productData.name}`;
        document.getElementById('modal-product-customizations').textContent = displayedCustomizations.length > 0 ? displayedCustomizations.join(', ') : 'Regular';
        document.getElementById('modal-product-quantity').textContent = productData.quantity;
        
        // Atur link untuk tombol "Cek Keranjang"
        modalCheckoutBtn.href = '/checkout'; // Arahkan ke halaman keranjang

        successModal.style.display = 'flex';
    }

    async function handleSubmit(event) {
        event.preventDefault();
        // ... (kode fetch Anda yang sudah benar tetap di sini) ...
        const addToCartUrl = form.dataset.addToCartUrl;
        const productId = document.getElementById('product-detail-name').dataset.productId;
        const quantity = parseInt(quantityElement.textContent, 10);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const customizations = Array.from(form.querySelectorAll('input:checked')).map(opt => {
            return opt.closest('.option-item').querySelector('.option-name').textContent.trim().replace(/👍/g, '').trim();
        });

        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Menambahkan...';

        try {
            const response = await fetch(addToCartUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity, customizations: customizations })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Gagal menambahkan produk.');

            // Panggil fungsi untuk menampilkan modal
            showSuccessModal({
                name: document.getElementById('product-detail-name').textContent,
                quantity: quantity,
                customizations: customizations
            });

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            addToCartBtn.disabled = false;
            calculateTotalPrice();
        }
    }

    function initializeEventListeners() {
        form.addEventListener('input', calculateTotalPrice);
        addToCartBtn.addEventListener('click', handleSubmit);

        document.getElementById('increase-qty').addEventListener('click', () => {
            quantityElement.textContent = parseInt(quantityElement.textContent, 10) + 1;
            calculateTotalPrice();
        });

        document.getElementById('decrease-qty').addEventListener('click', () => {
            let quantity = parseInt(quantityElement.textContent, 10);
            if (quantity > 1) {
                quantityElement.textContent = quantity - 1;
            }
            calculateTotalPrice();
        });

        // Event listener untuk menutup modal
        modalContinueBtn.addEventListener('click', () => successModal.style.display = 'none');
        successModal.addEventListener('click', (e) => {
            if (e.target === successModal) {
                successModal.style.display = 'none';
            }
        });
    }

    calculateTotalPrice();
    initializeEventListeners();
});