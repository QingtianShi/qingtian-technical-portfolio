<?php $navUser = currentAccount(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="/index.php">Peer Tutoring Hub</a>
    <div class="navbar-nav">
      <?php if ($navUser): ?>
        <?php if ($navUser['role'] === 'Administrator'): ?>
          <a class="nav-link" href="/php/users.php">Manage Users</a>
          <a class="nav-link" href="/php/requests.php">All Tutoring Requests</a>
        <?php else: ?>
          <a class="nav-link" href="/php/requests.php">My Requests</a>
          <a class="nav-link" href="/php/request-form.php">Request a Session</a>
        <?php endif; ?>
        <a class="nav-link" href="/php/profile.php">My Profile</a>
        <form method="post" action="/php/logout.php" class="m-0 p-0 bg-transparent shadow-none border-0">
          <?=csrfField()?><button type="submit" class="nav-link">Log Out</button>
        </form>
      <?php else: ?>
        <a class="nav-link" href="/php/login.php">Log In</a>
        <a class="nav-link" href="/php/register.php">Create Account</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
