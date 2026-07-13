<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $current = post('current_password');
    $new = post('new_password');
    $confirm = post('confirm_password');

    if (!password_verify($current, $admin['password'])) {
        flash_set('error', 'Current password is incorrect.');
    } elseif (strlen($new) < 6) {
        flash_set('error', 'New password must be at least 6 characters.');
    } elseif ($new !== $confirm) {
        flash_set('error', 'New password and confirmation do not match.');
    } else {
        Database::update('admins', ['password' => password_hash($new, PASSWORD_DEFAULT)], 'id=?', [$admin['id']]);
        flash_set('success', 'Password updated successfully.');
    }
    redirect('admin/index.php?page=password');
}
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="admin-card">
      <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">Current password</label><input type="password" class="form-control" name="current_password" required></div>
        <div class="mb-3"><label class="form-label">New password</label><input type="password" class="form-control" name="new_password" minlength="6" required></div>
        <div class="mb-3"><label class="form-label">Confirm new password</label><input type="password" class="form-control" name="confirm_password" minlength="6" required></div>
        <button class="btn btn-blue" type="submit">Update password</button>
      </form>
    </div>
  </div>
</div>
