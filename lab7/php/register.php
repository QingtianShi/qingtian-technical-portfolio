<?php
require_once __DIR__.'/../includes/auth.php';
if (currentAccount()) {
    go('/php/profile.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        registerStudent(authDatabase(),$_POST);
        $_SESSION['registered'] = true;
        go('/php/login.php');
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
        http_response_code(422);
    }
}
require __DIR__.'/../includes/header.php';
require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4">
<h1>Create Student Account</h1>
<?php if ($error !== ''): ?><p role="alert"><?=escapeHtml($error)?></p><?php endif; ?>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?>
    <label>Full name <input name="full_name" maxlength="120" value="<?=escapeHtml(accountText($_POST,'full_name'))?>" required></label>
    <label>Email <input type="email" name="email" maxlength="254" value="<?=escapeHtml(accountText($_POST,'email'))?>" required></label>
    <label>Password <input type="password" name="password" minlength="12" maxlength="72" autocomplete="new-password" required></label>
    <label>Confirm password <input type="password" name="confirmation" minlength="12" maxlength="72" autocomplete="new-password" required></label>
    <button type="submit">Create Account</button>
</form>
<a href="login.php">Log in</a>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
