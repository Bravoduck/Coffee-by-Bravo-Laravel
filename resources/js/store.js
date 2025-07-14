document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.store-header');
    const searchInput = document.getElementById('store-search-input');
    const searchIconBtn = document.getElementById('search-icon-btn');
    const title = header.querySelector('h1.header-title'); // Lebih spesifik
    
    const storeListContainer = document.querySelector('.store-list');
    const allStoreCards = storeListContainer.querySelectorAll('.store-card');
    const storeCountElement = document.querySelector('.store-count');
    const originalStoreCount = allStoreCards.length;

    if (!header || !searchInput || !searchIconBtn || !title) return;

    // Event listener untuk ikon pencarian
    searchIconBtn.addEventListener('click', () => {
        header.classList.add('search-active');
        title.style.display = 'none';
        searchInput.style.display = 'block';
        searchInput.focus();
        searchIconBtn.style.display = 'none';
    });

    // Event listener untuk input pencarian
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