document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input'); // Asumsi ID input pencarian adalah 'search-input'
    const storeListContainer = document.querySelector('.store-list');
    const allStoreCards = storeListContainer.querySelectorAll('.store-card');
    const storeCountElement = document.querySelector('.store-count');
    const originalStoreCount = allStoreCards.length;

    if (!searchInput) return;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();
        let storesFound = 0;

        allStoreCards.forEach(card => {
            const storeName = card.querySelector('.store-name').textContent.toLowerCase();
            const matches = storeName.includes(query);
            card.style.display = matches ? 'flex' : 'none';
            if(matches) {
                storesFound++;
            }
        });

        if (query) {
            storeCountElement.textContent = `${storesFound} Store ditemukan`;
        } else {
            storeCountElement.textContent = `${originalStoreCount} Store tersedia`;
        }
    });
});