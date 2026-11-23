<?php
require_once __DIR__.'/../includes/auth.php';
requireAccount('Administrator');
$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
$target = $id ? accountById(authDatabase(),$id) : false;
if (!$target) {
    http_response_code(404);
    exit('Account not found.');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        $action = accountText($_POST,'action');
        if ($action === 'delete') {
            refuseAccountDeletion(authDatabase(),$id);
        }
        changeAccountAccess(authDatabase(),$id,$action,accountText($_POST,'value'));
        $_SESSION['account_saved'] = true;
        go('manage-user.php?id='.$id);
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
        http_response_code(422);
    }
}
$saved = $_SESSION['account_saved'] ?? false;
unset($_SESSION['account_saved']);
$newStatus = $target['status'] === 'Active' ? 'Inactive' : 'Active';
require __DIR__.'/../includes/header.php';
require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4">
<h1>Manage <?=escapeHtml($target['full_name'])?></h1>
<p><?=escapeHtml($target['email'])?> · <?=escapeHtml($target['role'])?> · <?=escapeHtml($target['status'])?></p>
<?php if ($saved): ?><p role="status">Account updated.</p><?php endif; ?>
<?php if ($error !== ''): ?><p role="alert"><?=escapeHtml($error)?></p><?php endif; ?>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?><input type="hidden" name="action" value="role">
    <label>Role <select name="value">
    <?php foreach (['Student','Administrator'] as $role): ?><option <?=$target['role'] === $role ? 'selected' : ''?>><?=$role?></option><?php endforeach; ?>
    </select></label>
    <button type="submit">Save Role</button>
</form>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?><input type="hidden" name="action" value="status"><input type="hidden" name="value" value="<?=$newStatus?>">
    <p>Change account status to <?=$newStatus?>?</p>
    <button type="submit">Confirm Status Change</button>
</form>
<form method="post" class="tutoring-request-form">
    <?=csrfField()?><input type="hidden" name="action" value="delete">
    <p>Account deletion requires an approved retention policy. The system checks for linked tutoring requests.</p>
    <button type="submit">Confirm Delete</button>
    <a href="users.php">Cancel</a>
</form>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
