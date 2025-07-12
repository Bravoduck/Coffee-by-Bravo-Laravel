document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!cartItemsContainer) return;

    cartItemsContainer.addEventListener('click', function (event) {
        const target = event.target;
        const actionButton = target.closest('.increase-item-qty, .decrease-item-qty, .edit-item-btn');

        if (!actionButton) return;

        if (actionButton.matches('.increase-item-qty')) {
            handleQuantityChange(actionButton, 1);
        } else if (actionButton.matches('.decrease-item-qty')) {
            handleQuantityChange(actionButton, -1);
        } else if (actionButton.matches('.edit-item-btn')) {
            alert('Fitur Edit akan kita implementasikan di langkah berikutnya!');
        }
    });

    async function handleQuantityChange(button, change) {
        const cartItem = button.closest('.cart-item');
        const itemId = cartItem.dataset.id;
        const quantitySpan = cartItem.querySelector('.quantity-selector span');
        let currentQuantity = parseInt(quantitySpan.textContent, 10);
        const newQuantity = currentQuantity + change;

        cartItem.querySelectorAll('button').forEach(btn => btn.disabled = true);
        cartItem.style.opacity = '0.5';

        if (newQuantity < 1) {
            if (confirm('Anda yakin ingin menghapus item ini dari keranjang?')) {
                await sendRequest('/checkout/remove', { id: itemId });
            } else {
                cartItem.querySelectorAll('button').forEach(btn => btn.disabled = false);
                cartItem.style.opacity = '1';
            }
        } else {
            await sendRequest('/checkout/update', { id: itemId, quantity: newQuantity });
        }
    }

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
                const result = await response.json().catch(() => ({ message: 'Error tidak diketahui dari server.' }));
                throw new Error(result.message);
            }
            window.location.reload();
        } catch (error) {
            console.error('Request Error:', error);
            alert('Gagal memperbarui keranjang: ' + error.message);
            window.location.reload();
        }
    }
});