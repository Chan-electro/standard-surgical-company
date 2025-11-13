<?php
require_once __DIR__ . '/includes/functions.php';

$page = $_GET['page'] ?? 'home';
$routes = [
    'home' => __DIR__ . '/pages/home.php',
    'products' => __DIR__ . '/pages/products.php',
    'knowledge' => __DIR__ . '/pages/knowledge.php',
    'get-a-quote' => __DIR__ . '/pages/get-a-quote.php',
];

if (!array_key_exists($page, $routes)) {
    http_response_code(404);
    $page = 'home';
}

include __DIR__ . '/includes/header.php';
include $routes[$page];
include __DIR__ . '/includes/footer.php';
