<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/validation.php';

function startPage(string $title): void
{
    require_once __DIR__ . '/header.php';
    require_once __DIR__ . '/nav.php';
}

function failPage(int $status, string $message): never
{
    http_response_code($status);
    startPage($message);
    echo '<main><h1>' . e($message) . '</h1><a href="/card-list.php">Return to Gift Card List</a></main>';
    require_once __DIR__ . '/footer.php';
    exit;
}

function redirectTo(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function selectedCard(PDO $db): array
{
    $id = positiveId($_GET['card_id'] ?? null);
    if ($id === null) {
        failPage(400, 'Invalid card ID');
    }
    $query = $db->prepare('SELECT * FROM GIFTCARD WHERE cardId = :id');
    $query->execute(['id' => $id]);
    $card = $query->fetch();
    if ($card === false) {
        failPage(404, 'Gift card not found');
    }
    return $card;
}
