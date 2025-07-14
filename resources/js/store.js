document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.store-header');
    const searchInput = document.getElementById('store-search-input');
    const searchIconBtn = document.getElementById('header-search-btn'); // Pastikan ID ini benar
    const title = header.querySelector('h1.header-title');
    
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
            const storeAddress = card.querySelector('.store-address').textContent.toLowerCase();
            const matches = storeName.includes(query) || storeAddress.includes(query);
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

    // Event listener untuk tombol ESC atau klik di luar untuk menutup search
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && header.classList.contains('search-active')) {
            closeSearch();
        }
    });

    // Event listener untuk blur pada search input
    searchInput.addEventListener('blur', (e) => {
        // Delay untuk mencegah penutupan langsung saat klik
        setTimeout(() => {
            if (!searchInput.value.trim()) {
                closeSearch();
            }
        }, 150);
    });

    // Fungsi untuk menutup search
    function closeSearch() {
        header.classList.remove('search-active');
        title.style.display = 'block';
        searchInput.style.display = 'none';
        searchInput.value = '';
        searchIconBtn.style.display = 'block';
        
        // Reset tampilan semua store
        allStoreCards.forEach(card => {
            card.style.display = 'flex';
        });
        storeCountElement.textContent = `${originalStoreCount} Store tersedia`;
    }
});
