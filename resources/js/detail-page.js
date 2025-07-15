document.addEventListener('DOMContentLoaded', () => {
    const detailMain = document.querySelector('.detail-main');
    if (!detailMain) return;

    // === Variabel dan Elemen UI ===
    const isEditMode = sessionStorage.getItem('editMode') === 'true';
    const itemToEdit = isEditMode ? JSON.parse(sessionStorage.getItem('itemToEdit')) : null;
    const backBtn = document.querySelector('.detail-header .back-btn');
    const basePrice = parseInt(detailMain.dataset.basePrice, 10);
    const productId = document.getElementById('product-detail-name').dataset.productId;
    const form = document.getElementById('options-form');
    const quantityElement = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const toastNotification = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');
    let toastTimer;

    // === Logika Tombol Kembali ===
    if (backBtn) {
        backBtn.href = isEditMode ? '/checkout' : '/';
        backBtn.addEventListener('click', (e) => {
            if (isEditMode) {
                e.preventDefault();
                sessionStorage.removeItem('editMode');
                sessionStorage.removeItem('itemToEdit');
                window.location.href = backBtn.href;
            }
        });
    }

    // === FUNGSI-FUNGSI ===

    function showToast(message) {
        clearTimeout(toastTimer);
        toastMessage.textContent = message;
        toastNotification.className = 'toast-notification error show';
        toastTimer = setTimeout(() => {
            toastNotification.classList.remove('show');
        }, 3000);
    }

    function setupCheckboxLimits() {
        const toppingCheckboxes = document.querySelectorAll('#topping-group input[type="checkbox"]');
        toppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const checkedCount = document.querySelectorAll('#topping-group input[type="checkbox"]:checked').length;
                if (checkedCount > 2) {
                    showToast('Maksimal 2 topping yang bisa dipilih.');
                    e.target.checked = false;
                    calculateTotalPrice();
                }
            });
        });

        const syrupCheckboxes = document.querySelectorAll('#syrup-group input[type="checkbox"]');
        syrupCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                // Jika checkbox yang baru diklik itu dicentang
                if (e.target.checked) {
                    // Loop semua checkbox syrup
                    syrupCheckboxes.forEach(cb => {
                        // Batalkan centang pada checkbox lain
                        if (cb !== e.target) {
                            cb.checked = false;
                        }
                    });
                    calculateTotalPrice(); // Hitung ulang harga setelah perubahan
                }
            });
        });
    }

    function populateFormForEdit() {
        if (!itemToEdit) return;
        quantityElement.textContent = itemToEdit.quantity;
        const allInputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
        allInputs.forEach(input => {
            const label = input.closest('.option-item');
            if (!label) return;
            const nameSpan = label.querySelector('.option-name');
            const clone = nameSpan.cloneNode(true);
            if (clone.querySelector('.badge')) clone.querySelector('.badge').remove();
            const optionText = clone.textContent.trim().replace(/👍/g, '').trim();
            if (itemToEdit.customizations && itemToEdit.customizations.includes(optionText)) {
                input.checked = true;
            }
        });
    }

    function calculateTotalPrice() {
        let optionsPrice = 0;
        form.querySelectorAll('input:checked').forEach(option => {
            optionsPrice += parseInt(option.dataset.price, 10) || 0;
        });
        const quantity = parseInt(quantityElement.textContent, 10);
        const currentPrice = (basePrice + optionsPrice) * quantity;
        updateButtonPrice(currentPrice);
    }

    function updateButtonPrice(price) {
        const formattedPrice = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(price);
        const buttonText = isEditMode ? 'Update' : 'Tambah';
        addToCartBtn.innerHTML = `${buttonText} • ${formattedPrice.replace('Rp', 'Rp ')}`;
    }

    function handleSubmit(event) {
        event.preventDefault();
        const formElement = document.createElement('form');
        formElement.method = 'POST';
        formElement.action = '/cart/add';
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formElement.appendChild(csrfInput);
        const customizations = Array.from(form.querySelectorAll('input:checked')).map(opt => {
            const nameSpan = opt.closest('.option-item').querySelector('.option-name');
            const clone = nameSpan.cloneNode(true);
            if (clone.querySelector('.badge')) clone.querySelector('.badge').remove();
            return clone.textContent.trim().replace(/👍/g, '').trim();
        });
        const quantity = parseInt(quantityElement.textContent, 10);
        formElement.innerHTML += `<input type="hidden" name="product_id" value="${productId}">`;
        formElement.innerHTML += `<input type="hidden" name="quantity" value="${quantity}">`;
        customizations.forEach(cust => {
            formElement.innerHTML += `<input type="hidden" name="customizations[]" value="${cust}">`;
        });
        if (isEditMode && itemToEdit) {
            formElement.innerHTML += `<input type="hidden" name="old_cart_item_id" value="${itemToEdit.id}">`;
        }
        document.body.appendChild(formElement);
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Memproses...';
        sessionStorage.removeItem('editMode');
        sessionStorage.removeItem('itemToEdit');
        formElement.submit();
    }

    // === Inisialisasi Halaman ===
    if (isEditMode) {
        populateFormForEdit();
    }
    calculateTotalPrice();
    setupCheckboxLimits(); // Panggil validasi
    form.addEventListener('input', calculateTotalPrice);
    addToCartBtn.addEventListener('click', handleSubmit);
    document.getElementById('increase-qty').addEventListener('click', () => {
        quantityElement.textContent = parseInt(quantityElement.textContent, 10) + 1;
        calculateTotalPrice();
    });
    document.getElementById('decrease-qty').addEventListener('click', () => {
        let qty = parseInt(quantityElement.textContent, 10);
        if (qty > 1) {
            quantityElement.textContent = qty - 1;
            calculateTotalPrice();
        }
    });
});