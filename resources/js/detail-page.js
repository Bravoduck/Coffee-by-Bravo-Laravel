/**
 * @file detail-page.js
 * Mengelola semua logika untuk halaman detail produk versi Laravel.
 */
document.addEventListener('DOMContentLoaded', () => {
    const detailMain = document.querySelector('.detail-main');
    if (!detailMain) return;

    const basePrice = parseInt(detailMain.dataset.basePrice, 10);
    let currentPrice = 0;
    const form = document.getElementById('options-form');
    const quantityElement = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    // Elemen-elemen untuk Modal
    const successModal = document.getElementById('success-modal');
    const modalContinueBtn = document.getElementById('modal-continue-btn');

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

    // FUNGSI UNTUK MENAMPILKAN MODAL (dari kode lama)
    function showSuccessModal(productData) {
        const defaultOptions = ['Regular Ice', 'Normal Sweet', 'Normal Ice', 'Normal Shot', 'Milk'];
        const displayedCustomizations = productData.customizations.filter(c => !defaultOptions.includes(c));

        document.getElementById('modal-product-image').src = document.getElementById('product-detail-image').src;
        document.getElementById('modal-product-name').textContent = `Iced ${productData.name}`;
        document.getElementById('modal-product-customizations').textContent = displayedCustomizations.length > 0 ? displayedCustomizations.join(', ') : 'Regular';
        document.getElementById('modal-product-quantity').textContent = productData.quantity;
        
        successModal.style.display = 'flex';
    }

    async function handleSubmit(event) {
        event.preventDefault();
        const form = document.getElementById('options-form');
        const addToCartUrl = form.dataset.addToCartUrl;
        const productIdElement = document.getElementById('product-detail-name');
        const productId = productIdElement.dataset.productId;
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

            const cartFooterContainer = document.getElementById('cart-footer-container');
            if (cartFooterContainer) {
                cartFooterContainer.innerHTML = result.footer_html;
            }

            // Panggil fungsi untuk menampilkan modal
            showSuccessModal({
                name: productIdElement.textContent,
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
                calculateTotalPrice();
            }
        });

        // Event listener untuk menutup modal
        if(modalContinueBtn) {
            modalContinueBtn.addEventListener('click', () => successModal.style.display = 'none');
        }
        if(successModal) {
            successModal.addEventListener('click', (e) => {
                if (e.target === successModal) {
                    successModal.style.display = 'none';
                }
            });
        }
    }

    calculateTotalPrice();
    initializeEventListeners();
});