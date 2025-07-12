document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!cartItemsContainer) return;

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

        cartItem.querySelectorAll('button').forEach(btn => btn.disabled = true);

        if (newQuantity < 1) {
            if (confirm('Anda yakin ingin menghapus item ini dari keranjang?')) {
                await sendRequest('/checkout/remove', { id: itemId });
            } else {
                cartItem.querySelectorAll('button').forEach(btn => btn.disabled = false);
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
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Error server.');
            window.location.reload();
        } catch (error) {
            console.error('Request Error:', error);
            alert('Gagal memperbarui keranjang: ' + error.message);
            window.location.reload();
        }
    }
});