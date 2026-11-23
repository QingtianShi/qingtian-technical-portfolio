<?php
require_once __DIR__.'/../includes/auth.php';
$user = requireAccount();
$error = '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        if (($_POST['action'] ?? '') === 'password') {
            changeOwnPassword(authDatabase(),(int)$user['user_id'],$_POST);
            startAccountSession($user);
            $message = 'Password updated.';
        } elseif (($_POST['action'] ?? '') === 'profile') {
            updateOwnAccount(authDatabase(),(int)$user['user_id'],$_POST);
            $user = accountById(authDatabase(),(int)$user['user_id']);
            $message = 'Profile updated.';
        } else {
            throw new InvalidArgumentException('Select a valid profile action.');
        }
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
        http_response_code(422);
    }
}
require __DIR__.'/../includes/header.php';
require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4">
<h1>My Profile</h1>
<dl><dt>Role</dt><dd><?=escapeHtml($user['role'])?></dd><dt>Status</dt><dd><?=escapeHtml($user['status'])?></dd></dl>
<?php if ($error !== ''): ?><p role="alert"><?=escapeHtml($error)?></p><?php endif; ?>
<?php if ($message !== ''): ?><p role="status"><?=escapeHtml($message)?></p><?php endif; ?>
<h2>Edit profile</h2>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?><input type="hidden" name="action" value="profile">
    <label>Full name <input name="full_name" maxlength="120" value="<?=escapeHtml($user['full_name'])?>" required></label>
    <label>Email <input name="email" type="email" maxlength="254" value="<?=escapeHtml($user['email'])?>" required></label>
    <button type="submit">Save Changes</button>
</form>
<h2>Change password</h2>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?><input type="hidden" name="action" value="password">
    <label>Current password <input name="current_password" type="password" autocomplete="current-password" required></label>
    <label>New password <input name="password" type="password" minlength="12" maxlength="72" autocomplete="new-password" required></label>
    <label>Confirm new password <input name="confirmation" type="password" autocomplete="new-password" required></label>
    <button type="submit">Update Password</button>
</form>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
