<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../services/userService.php';
require_once __DIR__.'/../services/requestService.php';

session_start([
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
        || getenv('CODESPACES') === 'true',
]);
header('Cache-Control: no-store');

function go(string $location): never {
    header('Location: '.$location,true,303);
    exit;
}

function authDatabase(): PDO {
    static $connection;
    return $connection ??= openDatabase();
}

function csrfToken(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrfField(): string {
    return '<input type="hidden" name="csrf" value="'.escapeHtml(csrfToken()).'">';
}

function verifyCsrf(): void {
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], accountText($_POST,'csrf'))) {
        http_response_code(403);
        exit('Invalid form token. Reload the page and try again.');
    }
}

function startAccountSession(array $user): void {
    session_regenerate_id(true);
    $_SESSION = ['user_id'=>(int)$user['user_id'], 'last_activity'=>time()];
}

function endAccountSession(): void {
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    unset($cookie['lifetime']);
    $cookie['expires'] = time()-3600;
    setcookie(session_name(), '', $cookie);
    session_destroy();
}

function currentAccount(): array|false {
    static $account;
    if ($account !== null) {
        return $account;
    }
    if (!isset($_SESSION['user_id'])) {
        return $account = false;
    }
    $user = accountById(authDatabase(),$_SESSION['user_id']);
    if (!$user || $user['status'] !== 'Active' || time()-$_SESSION['last_activity'] > 1800) {
        endAccountSession();
        go('/php/login.php');
    }
    $_SESSION['last_activity'] = time();
    return $account = $user;
}

function requireAccount(?string $role = null): array {
    $user = currentAccount();
    if (!$user) {
        go('/php/login.php');
    }
    if ($role !== null && $user['role'] !== $role) {
        http_response_code(403);
        exit('You do not have permission to use this page.');
    }
    return $user;
}

function requestStudentId(array $user): int {
    $student = studentByAccount(authDatabase(),(int)$user['user_id']);
    if (!$student) {
        http_response_code(403);
        exit('Student profile is unavailable.');
    }
    return (int)$student['student_id'];
}

function requireVisibleRequest(int $id, array $user): array {
    $request = findRequest(authDatabase(),$id);
    if (!$request || ($user['role'] === 'Student' && (int)$request['student_id'] !== requestStudentId($user))) {
        http_response_code(404);
        exit('Request not found.');
    }
    return $request;
}
