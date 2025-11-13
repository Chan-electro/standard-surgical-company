<?php $pageTitle = 'Products | MyBrand Healthcare Equipment'; include 'header.php'; ?>
<section class="section products-hero">
    <div class="section-header">
        <h1>Products</h1>
        <p>Enhance your hospital space with our premium range of hospital furniture and critical-care equipment.</p>
    </div>
</section>
<section class="section products-page">
    <div class="products-layout">
        <aside class="filters-panel">
            <div class="filters-header">
                <h2>Filters</h2>
            </div>
            <div class="filter-group">
                <label for="product-search" class="filter-label">Search Products</label>
                <input type="search" id="product-search" placeholder="Search by name or code">
            </div>
            <div class="filter-group collapsible">
                <button class="collapsible-toggle" type="button" aria-expanded="true">
                    <span>By Product Type</span>
                    <span class="toggle-icon">−</span>
                </button>
                <div class="collapsible-content" data-filter-group="category">
                    <!-- Category checkboxes will be injected by main.js -->
                </div>
            </div>
        </aside>
        <div class="products-results">
            <div class="products-grid" id="products-grid">
                <!-- Product cards rendered by main.js -->
            </div>
            <div class="no-results" id="no-results" hidden>
                <p>No products match your filters.</p>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
