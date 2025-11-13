<?php
require_once __DIR__ . '/functions.php';

$page = $_GET['page'] ?? 'home';
$pageTitles = [
    'home' => BRAND_NAME . ' | Hospital Furniture Manufacturer',
    'products' => 'Products | ' . BRAND_NAME,
    'knowledge' => 'Knowledge Center | ' . BRAND_NAME,
    'get-a-quote' => 'Get a Quote | ' . BRAND_NAME,
];
$metaDescriptions = [
    'home' => 'aslams designs and manufactures innovative hospital furniture for ICUs, wards, and specialty care environments across India.',
    'products' => 'Explore hospital beds, stretchers, ICU furniture, and medical accessories engineered for clinical excellence.',
    'knowledge' => 'Insights and best practices on hospital furniture planning, infection control, and patient-centric design.',
    'get-a-quote' => 'Request a tailored quotation or explore career opportunities with aslams — Health Care Equipment.',
];
$title = $pageTitles[$page] ?? BRAND_NAME;
$description = $metaDescriptions[$page] ?? $metaDescriptions['home'];

function nav_is_active(string $target): bool
{
    $current = $_GET['page'] ?? 'home';
    return $current === $target;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES); ?>">
    <meta property="og:title" content="<?= $title; ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
    <meta property="og:site_name" content="<?= BRAND_NAME; ?>">
    <meta property="og:image" content="/assets/img/og-default.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $title; ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/assets/css/site.css">
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
</head>
<body>
<header class="site-header" role="banner">
    <div class="site-header__inner">
        <a class="brand" href="/?page=home">
            <span class="brand__title"><?= BRAND_NAME; ?></span>
            <span class="brand__tagline">Trusted hospital furniture partner</span>
        </a>
        <button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
        </button>
        <nav class="site-nav" role="navigation" aria-label="Primary">
            <ul id="primary-nav">
                <li><a class="<?= nav_is_active('home') ? 'is-active' : ''; ?>" href="/?page=home">Home</a></li>
                <li><a class="<?= nav_is_active('products') ? 'is-active' : ''; ?>" href="/?page=products">Products</a></li>
                <li><a class="<?= nav_is_active('knowledge') ? 'is-active' : ''; ?>" href="/?page=knowledge">Knowledge</a></li>
                <li><a class="<?= nav_is_active('get-a-quote') ? 'is-active' : ''; ?>" href="/?page=get-a-quote">Get a Quote</a></li>
            </ul>
        </nav>
        <a class="btn btn-primary" href="/?page=get-a-quote">Get a Quote</a>
    </div>
</header>
<main class="site-main" role="main">
