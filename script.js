const itemsPerPage = 6;
let currentPage = 1;
let currentCategory = 'all';

function renderGallery() {
    const items = document.querySelectorAll('.gallery .item');
    const filteredItems = Array.from(items).filter(item => 
        currentCategory === 'all' || item.classList.contains(currentCategory)
    );

    // Hide all items initially
    items.forEach(item => item.style.display = 'none');

    // Calculate Start and End indices
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;

    // Show only the items for the current page
    const visibleItems = filteredItems.slice(startIndex, endIndex);
    visibleItems.forEach(item => item.style.display = 'block');

    renderPagination(filteredItems.length);
}

function renderPagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = '';

    if (totalPages > 1) {
        // Prev Button
        const prevButton = document.createElement('button');
        prevButton.innerText = 'Prev';
        prevButton.disabled = currentPage === 1;
        prevButton.onclick = () => {
            currentPage--;
            renderGallery();
        };
        paginationContainer.appendChild(prevButton);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.innerText = i;
            if (i === currentPage) {
                pageButton.classList.add('active');
            }
            pageButton.onclick = () => {
                currentPage = i;
                renderGallery();
            };
            paginationContainer.appendChild(pageButton);
        }

        // Next Button
        const nextButton = document.createElement('button');
        nextButton.innerText = 'Next';
        nextButton.disabled = currentPage === totalPages;
        nextButton.onclick = () => {
            currentPage++;
            renderGallery();
        };
        paginationContainer.appendChild(nextButton);
    }
}

function filterGallery(category) {
    const buttons = document.querySelectorAll('.filters button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    currentCategory = category;
    currentPage = 1; // Reset to page 1 on filter change
    renderGallery();
}

function openLightbox(img) {
    document.getElementById('lightbox').style.display = 'flex';
    document.getElementById('lightboxImg').src = img.src;
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}

// Initialize gallery on load
window.onload = function() {
    if (document.querySelectorAll('.gallery .item').length > 0) {
        renderGallery();
    }
};
