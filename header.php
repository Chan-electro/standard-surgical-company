<?php
// header.php - shared site header and navigation
$pageTitle = $pageTitle ?? 'MyBrand Healthcare Equipment';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script defer src="/assets/js/main.js"></script>
</head>
<body>
<header class="site-header">
    <div class="nav-container">
        <div class="brand">
            <a href="/index.php" class="brand-link">MyBrand <span>Healthcare Equipment</span></a>
        </div>
        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav class="main-nav">
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/products.php">Products</a></li>
                <li><a href="/knowledge.php">Knowledge</a></li>
                <li><a href="/contact.php">Contact Us</a></li>
                <li><a href="/about.php">About Us</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <div class="header-socials">
                <a href="https://wa.me/0000000000" target="_blank" rel="noopener">WhatsApp</a>
                <a href="#" target="_blank" rel="noopener">LinkedIn</a>
                <a href="#" target="_blank" rel="noopener">Instagram</a>
                <a href="#" target="_blank" rel="noopener">Facebook</a>
            </div>
            <a class="btn btn-accent" href="/contact.php">Get a Quote</a>
        </div>
    </div>
</header>
<main class="page-content">
