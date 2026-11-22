<?php
require_once __DIR__.'/../includes/auth.php';
requireAccount('Administrator');
$search = trim(accountText($_GET,'search'));
$role = accountText($_GET,'role');
$status = accountText($_GET,'status');
$roles = ['Student','Administrator'];
$statuses = ['Active','Inactive'];
$accounts = searchAccounts(authDatabase(),$search,$role,$status);
require __DIR__.'/../includes/header.php';
require __DIR__.'/../includes/nav.php';
?>
<main class="container py-4">
<h1>Manage Users</h1>
<form method="get" class="row g-2 mb-3">
    <label class="col">Name or email <input class="form-control" name="search" value="<?=escapeHtml($search)?>"></label>
    <label class="col">Role <select class="form-select" name="role"><option value="">All roles</option>
        <?php foreach ($roles as $item): ?><option <?=$role === $item ? 'selected' : ''?>><?=$item?></option><?php endforeach; ?>
    </select></label>
    <label class="col">Status <select class="form-select" name="status"><option value="">All statuses</option>
        <?php foreach ($statuses as $item): ?><option <?=$status === $item ? 'selected' : ''?>><?=$item?></option><?php endforeach; ?>
    </select></label>
    <div class="col-auto align-self-end"><button class="btn btn-primary">Search</button></div>
</form>
<?php if (!$accounts): ?><p>No matching accounts.</p><?php else: ?>
<table class="table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($accounts as $account): ?>
<tr>
    <td><?=escapeHtml($account['full_name'])?></td><td><?=escapeHtml($account['email'])?></td>
    <td><?=escapeHtml($account['role'])?></td><td><?=escapeHtml($account['status'])?></td>
    <td><a href="manage-user.php?id=<?=(int)$account['user_id']?>">Manage</a></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
