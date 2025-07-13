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
    const modalCheckoutBtn = document.getElementById('modal-checkout-btn');

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
        
        // Saring kustomisasi untuk hanya menampilkan yang bukan default
        const displayedCustomizations = productData.customizations.filter(c => !defaultOptions.includes(c));

        document.getElementById('modal-product-image').src = document.getElementById('product-detail-image').src;
        document.getElementById('modal-product-name').textContent = `Iced ${productData.name}`;
        
        // Tampilkan kustomisasi yang sudah bersih, atau 'Regular' jika tidak ada
        document.getElementById('modal-product-customizations').textContent = displayedCustomizations.length > 0 ? displayedCustomizations.join(', ') : 'Regular';
        
        document.getElementById('modal-product-quantity').textContent = productData.quantity;
        modalCheckoutBtn.href = '/checkout';
        successModal.style.display = 'flex';
    }

    async function handleSubmit(event) {
        event.preventDefault();
        const addToCartUrl = form.dataset.addToCartUrl;
        const productIdElement = document.getElementById('product-detail-name');
        const productId = productIdElement.dataset.productId;
        const quantity = parseInt(quantityElement.textContent, 10);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // LOGIKA BARU UNTUK MEMBERSIHKAN TEKS KUSTOMISASI
        const customizations = Array.from(form.querySelectorAll('input:checked')).map(opt => {
            const nameSpan = opt.closest('.option-item').querySelector('.option-name');
            const clone = nameSpan.cloneNode(true);
            // Hapus elemen badge jika ada
            if (clone.querySelector('.badge')) {
                clone.querySelector('.badge').remove();
            }
            // Ambil teks bersihnya
            return clone.textContent.trim().replace(/👍/g, '').trim();
        });

        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Menambahkan...';

        try {
            const response = await fetch(addToCartUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity, customizations: customizations }) // Kirim data bersih
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Gagal menambahkan produk.');
            const cartFooterContainer = document.getElementById('cart-footer-container');
            if (cartFooterContainer) {
                cartFooterContainer.innerHTML = result.footer_html;
            }
            showSuccessModal({
                name: productIdElement.textContent,
                quantity: quantity,
                customizations: customizations // Gunakan data bersih untuk modal
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