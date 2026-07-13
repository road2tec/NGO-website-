<?php
require_once __DIR__ . '/includes/auth.php';

if (admin_logged_in()) {
    header('Location: ' . admin_url('index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = post('username');
    $password = post('password');
    $admin = Database::one("SELECT * FROM admins WHERE username=?", [$username]);

    $ok = false;
    if ($admin) {
        // Support both a bcrypt hash and the initial plaintext seed value.
        if (password_verify($password, $admin['password'])) {
            $ok = true;
        } elseif (!str_starts_with($admin['password'], '$2y$') && hash_equals($admin['password'], $password)) {
            // First login after install: upgrade the stored password to a bcrypt hash.
            $ok = true;
            Database::update('admins', ['password' => password_hash($password, PASSWORD_DEFAULT)], 'id=?', [$admin['id']]);
        }
    }

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        Database::update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id=?', [$admin['id']]);
        header('Location: ' . admin_url('index.php'));
        exit;
    }
    $error = 'Invalid username or password.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login - <?= e(setting('site_name')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-login-body d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card-ngo p-4 p-lg-5">
          <div class="text-center mb-4">
            <span class="brand-mark d-inline-flex mb-2"><i class="fa-solid fa-hands-holding-child"></i></span>
            <h4 class="fw-bold mb-0">Admin Panel</h4>
            <div class="small text-muted"><?= e(setting('site_name')) ?></div>
          </div>
          <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
          <form method="post">
            <?= csrf_field() ?>
            <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
            <button class="btn btn-blue w-100" type="submit">Log in</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
