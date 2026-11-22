<?php
require_once __DIR__.'/../includes/auth.php';
if (currentAccount()) {
    go('/php/requests.php');
}
$registered = $_SESSION['registered'] ?? false;
unset($_SESSION['registered']);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $user = authenticateAccount(authDatabase(),accountText($_POST,'email'),accountText($_POST,'password'));
    if ($user) {
        startAccountSession($user);
        go('/php/requests.php');
    }
    http_response_code(422);
    $error = 'The email or password is incorrect, or the account is inactive.';
}
require __DIR__.'/../includes/header.php';
require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4">
<h1>Log In</h1>
<?php if ($registered): ?><p role="status">Student account created. Log in to continue.</p><?php endif; ?>
<?php if ($error !== ''): ?><p role="alert"><?=escapeHtml($error)?></p><?php endif; ?>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?>
    <label>Email <input name="email" type="email" autocomplete="username" required></label>
    <label>Password <input name="password" type="password" autocomplete="current-password" required></label>
    <button type="submit">Log In</button>
</form>
<a href="register.php">Create Account</a>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
