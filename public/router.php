<?php
// Router for `php -S`; Apache production requests use .htaccess instead.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}

if ($path === '/api/shorten') {
    $_GET['route'] = 'api';
} elseif (preg_match('#^/([A-Za-z0-9]{6,8})$#', $path, $matches)) {
    $_GET['route'] = 'redirect';
    $_GET['code'] = $matches[1];
}

require __DIR__ . '/index.php';
