<?php
require_once __DIR__ . '/../includes/functions.php';

$q = sanitize($_GET['q'] ?? '');
$type = sanitize($_GET['type'] ?? '');
$spec = sanitize($_GET['spec'] ?? '');
$pageNumber = max(1, (int)($_GET['page_num'] ?? 1));
$featureParams = [
    'central_locking',
    'iv_pole',
    'space_saving',
    'pediatric',
    'tv_mount',
];
$railsMaterial = $_GET['rails_material'] ?? [];
$railsCount = $_GET['rails_count'] ?? [];

$where = [];
$params = [];

if ($q !== '') {
    $where[] = '(name LIKE :q OR sku LIKE :q)';
    $params[':q'] = "%$q%";
}
if ($type !== '') {
    $where[] = '(category = :category OR subtype = :category)';
    $params[':category'] = $type;
}
if ($spec !== '') {
    $where[] = 'FIND_IN_SET(:spec, specialties)';
    $params[':spec'] = $spec;
}
foreach ($featureParams as $feature) {
    if (isset($_GET[$feature]) && $_GET[$feature] === '1') {
        $where[] = "$feature = 1";
    }
}
if (!empty($railsMaterial)) {
    $placeholders = [];
    foreach ((array)$railsMaterial as $idx => $material) {
        $key = ":rails_material_$idx";
        $placeholders[] = $key;
        $params[$key] = sanitize($material);
    }
    if ($placeholders) {
        $where[] = 'rails_material IN (' . implode(',', $placeholders) . ')';
    }
}
if (!empty($railsCount)) {
    $placeholders = [];
    foreach ((array)$railsCount as $idx => $count) {
        $key = ":rails_count_$idx";
        $placeholders[] = $key;
        $params[$key] = (int)$count;
    }
    if ($placeholders) {
        $where[] = 'rails_count IN (' . implode(',', $placeholders) . ')';
    }
}

try {
    $pdo = get_db();
    $condition = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $condition");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $pagination = paginate($pageNumber, ITEMS_PER_PAGE, $total);

    $sql = "SELECT sku, name, category, subtype, rails_material, rails_count, central_locking, iv_pole, urine_bag_holder, space_saving, pediatric, tv_mount, hero_image, gallery, specialties, created_at FROM products $condition ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
    $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

    $filters = [
        'categories' => $pdo->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN),
        'subtypes' => $pdo->query('SELECT DISTINCT subtype FROM products ORDER BY subtype')->fetchAll(PDO::FETCH_COLUMN),
        'specialties' => $pdo->query('SELECT DISTINCT specialties FROM products WHERE specialties IS NOT NULL AND specialties <> ""')->fetchAll(PDO::FETCH_COLUMN),
    ];
} catch (Throwable $e) {
    $products = json_decode(file_get_contents(__DIR__ . '/../data/products.json'), true) ?? [];
    $total = count($products);
    $pagination = paginate($pageNumber, ITEMS_PER_PAGE, $total);
    $products = array_slice($products, $pagination['offset'], $pagination['per_page']);
    $specialties = [];
    foreach ($products as $product) {
        $specialties = array_merge($specialties, array_filter(array_map('trim', explode(',', $product['specialties'] ?? ''))));
    }
    $filters = [
        'categories' => array_unique(array_filter(array_column($products, 'category'))),
        'subtypes' => array_unique(array_filter(array_column($products, 'subtype'))),
        'specialties' => array_unique($specialties),
    ];
}

$specialtiesOptions = [];
foreach ($filters['specialties'] ?? [] as $item) {
    if (!is_string($item)) {
        continue;
    }
    $parts = array_map('trim', explode(',', $item));
    foreach ($parts as $part) {
        if ($part !== '' && !in_array($part, $specialtiesOptions, true)) {
            $specialtiesOptions[] = $part;
        }
    }
}
?>
<section class="section" aria-labelledby="products-heading" data-animate>
    <div class="section__header">
        <h1 id="products-heading">Hospital Furniture Products</h1>
        <p>Discover modular, ergonomic hospital furniture engineered for demanding clinical environments.</p>
    </div>
    <div class="products-layout">
        <aside class="filter-panel" aria-label="Product filters" data-animate>
            <form method="get" action="" class="filter-form">
                <input type="hidden" name="page" value="products">
                <input type="hidden" name="page_num" value="1">
                <div class="filter-group">
                    <label for="filter-q">Search</label>
                    <input id="filter-q" type="search" name="q" value="<?= $q; ?>" placeholder="Search by name or SKU" autocomplete="off">
                </div>
                <div class="filter-group">
                    <label for="filter-type">Product Type</label>
                    <select id="filter-type" name="type">
                        <option value="">All Types</option>
                        <?php foreach ($filters['categories'] as $category): ?>
                            <option value="<?= sanitize($category); ?>" <?= $type === $category ? 'selected' : ''; ?>><?= sanitize($category); ?></option>
                        <?php endforeach; ?>
                        <?php foreach ($filters['subtypes'] as $subtype): ?>
                            <?php if ($subtype): ?>
                                <option value="<?= sanitize($subtype); ?>" <?= $type === $subtype ? 'selected' : ''; ?>><?= sanitize($subtype); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-spec">Medical Specialty</label>
                    <select id="filter-spec" name="spec">
                        <option value="">All Specialties</option>
                        <?php foreach ($specialtiesOptions as $option): ?>
                            <option value="<?= sanitize($option); ?>" <?= $spec === $option ? 'selected' : ''; ?>><?= sanitize($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <fieldset class="filter-group">
                    <legend>Features</legend>
                    <?php foreach ($featureParams as $feature): ?>
                        <div class="checkbox">
                            <input id="feature-<?= $feature; ?>" type="checkbox" name="<?= $feature; ?>" value="1" <?= isset($_GET[$feature]) && $_GET[$feature] === '1' ? 'checked' : ''; ?>>
                            <label for="feature-<?= $feature; ?>"><?= ucwords(str_replace('_', ' ', $feature)); ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
                <fieldset class="filter-group">
                    <legend>Rails Material</legend>
                    <?php foreach (['SS', 'MS', 'Polymer'] as $material): ?>
                        <div class="checkbox">
                            <input id="rails-material-<?= strtolower($material); ?>" type="checkbox" name="rails_material[]" value="<?= $material; ?>" <?= in_array($material, (array)$railsMaterial, true) ? 'checked' : ''; ?>>
                            <label for="rails-material-<?= strtolower($material); ?>"><?= $material; ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
                <fieldset class="filter-group">
                    <legend>Rails Count</legend>
                    <?php foreach ([2, 4] as $count): ?>
                        <div class="checkbox">
                            <input id="rails-count-<?= $count; ?>" type="checkbox" name="rails_count[]" value="<?= $count; ?>" <?= in_array((string)$count, array_map('strval', (array)$railsCount), true) ? 'checked' : ''; ?>>
                            <label for="rails-count-<?= $count; ?>"><?= $count; ?> Rails</label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
                <div class="filter-actions">
                    <button class="btn btn-primary" type="submit">Apply Filters</button>
                    <a class="btn btn-outline" href="/?page=products">Reset</a>
                </div>
            </form>
        </aside>
        <div class="products-results" data-animate>
            <p class="products-count" role="status"><?= $total; ?> product<?= $total === 1 ? '' : 's'; ?> found</p>
            <div class="grid grid--products">
                <?php foreach ($products as $product): ?>
                    <?php
                        $features = [];
                        foreach (['central_locking' => 'Central Locking', 'iv_pole' => 'IV Pole', 'space_saving' => 'Space Saving', 'pediatric' => 'Pediatric', 'tv_mount' => 'TV Mount'] as $key => $label) {
                            if (!empty($product[$key])) {
                                $features[] = $label;
                            }
                        }
                        if (!empty($product['rails_material'])) {
                            $features[] = $product['rails_material'] . ' Rails';
                        }
                        if (!empty($product['rails_count'])) {
                            $features[] = $product['rails_count'] . ' Rails';
                        }
                        $jsonLd = [
                            '@context' => 'https://schema.org',
                            '@type' => 'Product',
                            'name' => $product['name'],
                            'sku' => $product['sku'],
                            'category' => $product['category'],
                            'image' => $product['hero_image'],
                        ];
                    ?>
                    <article class="product-card" itemscope itemtype="https://schema.org/Product">
                        <meta itemprop="sku" content="<?= sanitize($product['sku']); ?>">
                        <a class="product-card__media" href="/?page=products&amp;q=<?= urlencode($product['sku']); ?>" aria-label="View details for <?= sanitize($product['name']); ?>">
                            <img src="<?= sanitize($product['hero_image'] ?: '/assets/img/product-placeholder.jpg'); ?>" alt="<?= sanitize($product['name']); ?>" loading="lazy" width="320" height="220" itemprop="image">
                        </a>
                        <div class="product-card__body">
                            <h2 itemprop="name"><?= sanitize($product['name']); ?></h2>
                            <p class="product-card__meta">SKU: <span itemprop="productID"><?= sanitize($product['sku']); ?></span></p>
                            <p class="product-card__meta">Type: <?= sanitize($product['category']); ?><?= $product['subtype'] ? ' · ' . sanitize($product['subtype']) : ''; ?></p>
                            <?php if ($features): ?>
                                <ul class="feature-badges">
                                    <?php foreach ($features as $feature): ?>
                                        <li><?= sanitize($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <a class="btn btn-small" href="/?page=get-a-quote&amp;product=<?= urlencode($product['sku']); ?>">Know More</a>
                        </div>
                        <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="pagination" role="navigation" aria-label="Products pagination">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php $query = array_merge($_GET, ['page' => 'products', 'page_num' => $i]); ?>
                        <?php $url = '/?' . http_build_query($query); ?>
                        <a class="<?= $pagination['page'] === $i ? 'is-active' : ''; ?>" href="<?= $url; ?>" aria-current="<?= $pagination['page'] === $i ? 'page' : 'false'; ?>">Page <?= $i; ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</section>
