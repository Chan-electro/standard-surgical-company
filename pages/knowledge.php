<?php
require_once __DIR__ . '/../includes/functions.php';

$category = sanitize($_GET['category'] ?? '');

try {
    $pdo = get_db();
    if ($category) {
        $stmt = $pdo->prepare('SELECT slug, title, cover_image, excerpt, content, category, published_at FROM posts WHERE category = :category ORDER BY published_at DESC');
        $stmt->execute([':category' => $category]);
    } else {
        $stmt = $pdo->query('SELECT slug, title, cover_image, excerpt, content, category, published_at FROM posts ORDER BY published_at DESC');
    }
    $posts = $stmt->fetchAll();
    $categories = $pdo->query('SELECT DISTINCT category FROM posts ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    $posts = json_decode(file_get_contents(__DIR__ . '/../data/posts.json'), true) ?? [];
    $categories = array_unique(array_filter(array_column($posts, 'category')));
    if ($category) {
        $posts = array_values(array_filter($posts, function ($post) use ($category) {
            return isset($post['category']) && $post['category'] === $category;
        }));
    }
}
?>
<section class="section" aria-labelledby="knowledge-heading" data-animate>
    <div class="section__header">
        <h1 id="knowledge-heading">Knowledge Center</h1>
        <p>Field-tested advice on designing and maintaining high-performance hospital furniture.</p>
    </div>
    <div class="category-chips" role="toolbar" aria-label="Knowledge categories" data-animate>
        <a class="chip <?= $category === '' ? 'is-active' : ''; ?>" href="/?page=knowledge">All</a>
        <?php foreach ($categories as $cat): ?>
            <a class="chip <?= $category === $cat ? 'is-active' : ''; ?>" href="/?page=knowledge&amp;category=<?= urlencode($cat); ?>"><?= sanitize($cat); ?></a>
        <?php endforeach; ?>
    </div>
    <div class="grid grid--posts" data-animate>
        <?php foreach ($posts as $post): ?>
            <?php
                $excerpt = $post['excerpt'] ?? excerpt($post['content'] ?? '', 240);
                $jsonLd = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $post['title'],
                    'datePublished' => $post['published_at'] ?? date('Y-m-d'),
                    'author' => [
                        '@type' => 'Organization',
                        'name' => BRAND_NAME,
                    ],
                    'image' => $post['cover_image'] ?? '/assets/img/post-placeholder.jpg',
                ];
            ?>
            <article class="post-card" itemscope itemtype="https://schema.org/Article">
                <a class="post-card__media" href="/?page=knowledge&amp;slug=<?= urlencode($post['slug'] ?? ''); ?>">
                    <img src="<?= sanitize($post['cover_image'] ?? '/assets/img/post-placeholder.jpg'); ?>" alt="<?= sanitize($post['title']); ?>" loading="lazy" width="360" height="240" itemprop="image">
                </a>
                <div class="post-card__body">
                    <h2 itemprop="headline"><?= sanitize($post['title']); ?></h2>
                    <p><?= sanitize($excerpt); ?></p>
                    <p class="post-card__meta">Published on <time datetime="<?= sanitize($post['published_at'] ?? date('Y-m-d')); ?>" itemprop="datePublished"><?= date('F j, Y', strtotime($post['published_at'] ?? date('Y-m-d'))); ?></time></p>
                    <a class="btn btn-small" href="/?page=knowledge&amp;slug=<?= urlencode($post['slug'] ?? ''); ?>">Read more</a>
                    <meta itemprop="author" content="<?= BRAND_NAME; ?>">
                </div>
                <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
            </article>
        <?php endforeach; ?>
    </div>
</section>
