<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/') {
    header('Location: /card-list.php');
    exit;
}
if (in_array($path, [
    '/card-list.php', '/card-details.php', '/card-add.php', '/card-update.php',
    '/card-delete.php', '/tests/validation-tests.php', '/css/styles.css', '/images/gift-card.svg',
], true)) {
    return false;
}
http_response_code(404);
echo 'Page not found';
