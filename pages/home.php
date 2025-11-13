<?php
require_once __DIR__ . '/../includes/functions.php';

$products = [];
$posts = [];
$testimonials = [];
$clients = [];

try {
    $pdo = get_db();
    $productsStmt = $pdo->query('SELECT sku, name, hero_image, category FROM products ORDER BY created_at DESC LIMIT 4');
    $products = $productsStmt->fetchAll();
    $postsStmt = $pdo->query('SELECT slug, title, cover_image, excerpt, published_at FROM posts ORDER BY published_at DESC LIMIT 4');
    $posts = $postsStmt->fetchAll();
    $testimonialsStmt = $pdo->query('SELECT author_name, role_title, message FROM testimonials ORDER BY created_at DESC LIMIT 5');
    $testimonials = $testimonialsStmt->fetchAll();
    $clientsStmt = $pdo->query('SELECT name, logo, website FROM clients ORDER BY display_order ASC');
    $clients = $clientsStmt->fetchAll();
} catch (Throwable $e) {
    $products = json_decode(file_get_contents(__DIR__ . '/../data/products.json'), true) ?? [];
    $posts = json_decode(file_get_contents(__DIR__ . '/../data/posts.json'), true) ?? [];
    $testimonials = json_decode(file_get_contents(__DIR__ . '/../data/testimonials.json'), true) ?? [];
    $clients = json_decode(file_get_contents(__DIR__ . '/../data/clients.json'), true) ?? [];
}

?>
<section class="hero" aria-labelledby="home-hero-title">
    <div class="hero__content">
        <div class="hero__text" data-animate>
            <span class="hero__eyebrow">Trusted by healing spaces across India</span>
            <h1 id="home-hero-title">Warm, Patient-First Furniture for Modern Hospitals</h1>
            <p><?= BRAND_NAME; ?> curates cozy, human-centered environments with ergonomic beds, modular storage, and calming finishes that help teams deliver their best care.</p>
            <ul class="hero__features">
                <li>Calming palettes designed with clinicians</li>
                <li>Antimicrobial finishes and intuitive controls</li>
                <li>Dedicated service engineers available nationwide</li>
            </ul>
            <div class="hero__actions">
                <a class="btn btn-primary" href="/?page=products">Explore Products</a>
                <a class="btn btn-outline" href="/?page=get-a-quote">Request a Quote</a>
            </div>
            <dl class="hero__stats">
                <?php foreach (HOME_STATS as $stat): ?>
                    <div class="hero__stat">
                        <dt><?= sanitize($stat['label']); ?></dt>
                        <dd><?= sanitize($stat['value']); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
        <div class="hero__visual" data-animate>
            <picture>
                <source srcset="/assets/img/hero-bed@2x.webp 2x, /assets/img/hero-bed.webp 1x" type="image/webp">
                <img src="/assets/img/hero-bed.jpg" alt="Healthcare professionals adjusting an ICU bed" width="680" height="520">
            </picture>
        </div>
    </div>
</section>
<section class="section" aria-labelledby="section-solutions" data-animate>
    <div class="section__header">
        <h2 id="section-solutions">Complete Clinical Furniture Solutions</h2>
        <p>Modular hospital furniture programs designed for fast deployment, infection control, and streamlined care delivery.</p>
    </div>
    <div class="solutions-grid">
        <article class="card">
            <img src="/assets/img/solution-icu.jpg" alt="ICU bed with monitoring equipment" loading="lazy" width="320" height="220">
            <h3>Critical Care Suites</h3>
            <p>Integrated ICU bed systems with smart rails, remote controls, and nurse call integration.</p>
        </article>
        <article class="card">
            <img src="/assets/img/solution-emergency.jpg" alt="Emergency department stretchers lined up" loading="lazy" width="320" height="220">
            <h3>Emergency Readiness</h3>
            <p>Rapid-transfer stretchers, wheelchairs, and crash carts built for the ER environment.</p>
        </article>
        <article class="card">
            <img src="/assets/img/solution-ward.jpg" alt="General ward with hospital beds and lockers" loading="lazy" width="320" height="220">
            <h3>Patient Wards</h3>
            <p>Ergonomic beds, bedside lockers, and overbed tables to improve patient experience.</p>
        </article>
        <article class="card">
            <img src="/assets/img/solution-rehab.jpg" alt="Rehabilitation center furniture" loading="lazy" width="320" height="220">
            <h3>Rehabilitation Suites</h3>
            <p>Therapy couches and adjustable seating for physiotherapy and long-term care facilities.</p>
        </article>
    </div>
</section>
<section class="section" aria-labelledby="section-clients" data-component="client-carousel" data-animate>
    <div class="section__header">
        <h2 id="section-clients">Trusted by Leading Hospitals</h2>
        <p>Healthcare networks rely on our manufacturing excellence and nationwide service teams.</p>
    </div>
    <div class="client-carousel" role="list" aria-label="Client logos">
        <?php foreach ($clients as $client): ?>
            <div class="client-carousel__item" role="listitem">
                <img src="<?= sanitize($client['logo']); ?>" alt="<?= sanitize($client['name']); ?> logo" loading="lazy" width="180" height="80">
            </div>
        <?php endforeach; ?>
    </div>
</section>
<section class="section" aria-labelledby="section-products" data-animate>
    <div class="section__header">
        <h2 id="section-products">Featured Hospital Furniture</h2>
        <p>Newest launches designed for safety, hygiene, and effortless maintenance.</p>
    </div>
    <div class="grid grid--products">
        <?php foreach ($products as $product): ?>
            <article class="product-card" itemscope itemtype="https://schema.org/Product">
                <meta itemprop="sku" content="<?= sanitize($product['sku']); ?>">
                <a class="product-card__media" href="/?page=products&amp;q=<?= urlencode($product['sku']); ?>">
                    <img src="<?= sanitize($product['hero_image'] ?? '/assets/img/product-placeholder.jpg'); ?>" alt="<?= sanitize($product['name']); ?>" loading="lazy" width="320" height="220" itemprop="image">
                </a>
                <div class="product-card__body">
                    <h3 itemprop="name"><?= sanitize($product['name']); ?></h3>
                    <p class="product-card__meta">SKU: <span itemprop="productID"><?= sanitize($product['sku']); ?></span></p>
                    <p class="product-card__meta">Category: <?= sanitize($product['category'] ?? 'Hospital Furniture'); ?></p>
                    <a class="btn btn-small" href="/?page=products&amp;q=<?= urlencode($product['sku']); ?>">Know More</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<section class="section" aria-labelledby="section-knowledge" data-animate>
    <div class="section__header">
        <h2 id="section-knowledge">Knowledge Center</h2>
        <p>Practical guidance from our hospital furniture consultants and engineers.</p>
    </div>
    <div class="grid grid--posts">
        <?php foreach ($posts as $post): ?>
            <article class="post-card" itemscope itemtype="https://schema.org/Article">
                <a class="post-card__media" href="/?page=knowledge&amp;slug=<?= urlencode($post['slug'] ?? ''); ?>">
                    <img src="<?= sanitize($post['cover_image'] ?? '/assets/img/post-placeholder.jpg'); ?>" alt="<?= sanitize($post['title']); ?>" loading="lazy" width="320" height="220" itemprop="image">
                </a>
                <div class="post-card__body">
                    <h3 itemprop="headline"><?= sanitize($post['title']); ?></h3>
                    <p><?= excerpt($post['excerpt'] ?? ($post['content'] ?? ''), 160); ?></p>
                    <a class="btn btn-small" href="/?page=knowledge&amp;slug=<?= urlencode($post['slug'] ?? ''); ?>">Read more</a>
                    <meta itemprop="datePublished" content="<?= sanitize($post['published_at'] ?? date('Y-m-d')); ?>">
                    <meta itemprop="author" content="<?= BRAND_NAME; ?>">
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<section class="section section--accent" aria-labelledby="section-testimonials" data-animate>
    <div class="section__header">
        <h2 id="section-testimonials">Voices from Our Partners</h2>
        <p>Hear from hospital administrators and clinicians who work with us every day.</p>
    </div>
    <?php if (!empty($testimonials)): ?>
        <div class="testimonial-slider" data-component="testimonial-slider" role="region" aria-live="polite" tabindex="0">
            <button class="slider-control prev" type="button" aria-label="Previous testimonial">&#10094;</button>
            <div class="testimonial-slider__track">
                <?php foreach ($testimonials as $testimonial): ?>
                    <figure class="testimonial">
                        <blockquote>“<?= sanitize($testimonial['message']); ?>”</blockquote>
                        <figcaption>
                            <span class="testimonial__name"><?= sanitize($testimonial['author_name']); ?></span>
                            <span class="testimonial__role"><?= sanitize($testimonial['role_title']); ?></span>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
            <button class="slider-control next" type="button" aria-label="Next testimonial">&#10095;</button>
        </div>
    <?php else: ?>
        <p>Testimonials coming soon.</p>
    <?php endif; ?>
</section>
<section class="section section--cta" aria-labelledby="section-cta" data-animate>
    <div class="section__header">
        <h2 id="section-cta">Partner with <?= BRAND_NAME; ?></h2>
    </div>
    <div class="cta-grid">
        <article class="cta-card">
            <h3>Join our Fast-Growing Team</h3>
            <p>We are hiring production engineers, service technicians, and sales specialists across India.</p>
            <a class="btn btn-outline" href="/?page=get-a-quote&amp;form=careers">View Opportunities</a>
        </article>
        <article class="cta-card">
            <h3>Become a Dealer/Agent</h3>
            <p>Expand your portfolio with advanced hospital furniture supported by nationwide logistics.</p>
            <a class="btn btn-primary" href="/?page=get-a-quote&amp;form=dealer">Start the Conversation</a>
        </article>
    </div>
</section>
