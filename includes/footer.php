<?php require_once __DIR__ . '/config.php'; ?>
</main>
<footer class="site-footer" role="contentinfo">
    <div class="site-footer__contact">
        <h2 class="site-footer__heading">Connect with <?= BRAND_NAME; ?></h2>
        <p><span class="label">Address:</span> <?= ADDRESS; ?></p>
        <p><span class="label">Phone:</span> <a href="tel:<?= PHONE; ?>"><?= PHONE_DISPLAY; ?></a></p>
        <p><span class="label">Email:</span> <a href="mailto:<?= EMAIL_PRIMARY; ?>"><?= EMAIL_PRIMARY; ?></a></p>
    </div>
    <div class="site-footer__meta">
        <p>&copy; <?= date('Y'); ?> <?= BRAND_NAME; ?>. All rights reserved.</p>
    </div>
</footer>
<script src="/assets/js/site.js" defer></script>
</body>
</html>
