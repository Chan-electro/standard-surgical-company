// assets/js/main.js
// Update /assets/data/products.json with your product objects using the documented shape.
// Example structure:
// [
//   {
//     "code": "B5RS00000065",
//     "title": "Suite Beds: 5-Function Single Control Electrical Bed",
//     "category": "Hospital Beds",
//     "url": "https://www.aslamenterprises.com/products/...",
//     "image": "/assets/img/products/b5rs00000065.jpg"
//   }
// ]

(function () {
    const placeholderImage = '/assets/img/placeholder-product.jpg';

    document.addEventListener('DOMContentLoaded', () => {
        setupNavigation();
        setupCollapsibles();
        initProductListing();
    });

    function setupNavigation() {
        const navToggle = document.querySelector('.nav-toggle');
        const mainNav = document.querySelector('.main-nav');

        if (!navToggle || !mainNav) return;

        navToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
            const expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', (!expanded).toString());
        });
    }

    function setupCollapsibles() {
        document.querySelectorAll('.collapsible-toggle').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const content = toggle.parentElement?.querySelector('.collapsible-content');
                if (!content) return;
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', (!expanded).toString());
                toggle.querySelector('.toggle-icon').textContent = expanded ? '+' : '−';
                content.hidden = expanded;
            });
        });
    }

    function initProductListing() {
        const grid = document.getElementById('products-grid');
        if (!grid) {
            return;
        }

        const noResults = document.getElementById('no-results');
        const searchInput = document.getElementById('product-search');
        const categoryContainer = document.querySelector('[data-filter-group="category"]');

        let products = [];
        let selectedCategories = new Set();
        let searchTerm = '';

        fetch('/assets/data/products.json', {
            headers: {
                'Cache-Control': 'no-cache'
            }
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Unable to load products.json');
                }
                return response.json();
            })
            .then((data) => {
                if (!Array.isArray(data)) {
                    throw new Error('Invalid product data format');
                }
                products = data;
                renderCategoryFilters(data, categoryContainer, selectedCategories, () => {
                    renderProducts(products, grid, noResults, searchTerm, selectedCategories);
                });
                renderProducts(products, grid, noResults, searchTerm, selectedCategories);
            })
            .catch((error) => {
                console.error(error);
                grid.innerHTML = '<p class="no-results">Unable to load products at the moment.</p>';
            });

        if (searchInput) {
            searchInput.addEventListener('input', (event) => {
                searchTerm = event.target.value.trim().toLowerCase();
                renderProducts(products, grid, noResults, searchTerm, selectedCategories);
            });
        }
    }

    function renderCategoryFilters(data, container, selectedCategories, onChange) {
        if (!container) return;

        const categories = Array.from(new Set(data.map((item) => item.category).filter(Boolean))).sort();
        if (!categories.length) {
            container.innerHTML = '<p class="filter-empty">No categories found.</p>';
            return;
        }

        const list = document.createElement('div');
        list.className = 'checkbox-list';

        categories.forEach((category) => {
            const id = `filter-${category.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`;
            const wrapper = document.createElement('label');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = category;
            checkbox.id = id;
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    selectedCategories.add(category);
                } else {
                    selectedCategories.delete(category);
                }
                onChange();
            });

            const span = document.createElement('span');
            span.textContent = category;

            wrapper.appendChild(checkbox);
            wrapper.appendChild(span);
            list.appendChild(wrapper);
        });

        container.innerHTML = '';
        container.appendChild(list);
    }

    function renderProducts(products, grid, noResults, searchTerm, selectedCategories) {
        if (!grid) return;

        const activeCategories = Array.from(selectedCategories);
        const filtered = products.filter((product) => {
            const textMatch = !searchTerm ||
                product.title?.toLowerCase().includes(searchTerm) ||
                product.code?.toLowerCase().includes(searchTerm) ||
                product.category?.toLowerCase().includes(searchTerm);

            const categoryMatch = !activeCategories.length || (product.category && selectedCategories.has(product.category));

            return textMatch && categoryMatch;
        });

        if (!filtered.length) {
            grid.innerHTML = '';
            if (noResults) {
                noResults.hidden = false;
            }
            return;
        }

        if (noResults) {
            noResults.hidden = true;
        }

        const fragment = document.createDocumentFragment();

        filtered.forEach((product) => {
            const card = document.createElement('article');
            card.className = 'product-card';

            const img = document.createElement('img');
            img.src = product.image || placeholderImage;
            img.alt = product.title || 'Product image';
            img.onerror = function () {
                this.onerror = null;
                this.src = placeholderImage;
            };

            const body = document.createElement('div');
            body.className = 'product-body';

            const code = document.createElement('div');
            code.className = 'product-code';
            code.textContent = product.code || '';

            const title = document.createElement('h3');
            title.textContent = product.title || 'Untitled Product';

            const category = document.createElement('div');
            category.className = 'product-category';
            category.textContent = product.category ? `Category: ${product.category}` : 'Category: —';

            const button = document.createElement('a');
            button.className = 'btn btn-accent';
            button.href = product.url || '#';
            button.target = '_blank';
            button.rel = 'noopener';
            button.textContent = 'Know More';

            body.appendChild(code);
            body.appendChild(title);
            body.appendChild(category);
            body.appendChild(button);

            card.appendChild(img);
            card.appendChild(body);
            fragment.appendChild(card);
        });

        grid.innerHTML = '';
        grid.appendChild(fragment);
    }
})();
