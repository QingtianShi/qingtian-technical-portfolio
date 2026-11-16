<?php
declare(strict_types=1);

function database(): PDO
{
    $pdo = new PDO('sqlite:' . __DIR__ . '/../database/rewards.db', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA busy_timeout = 3000');
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}
