<?php
$stats = [
    'Members (pending)'    => (int) Database::value("SELECT COUNT(*) FROM members WHERE status='pending'"),
    'Members (approved)'   => (int) Database::value("SELECT COUNT(*) FROM members WHERE status='approved'"),
    'Active projects'      => (int) Database::value("SELECT COUNT(*) FROM projects WHERE type IN ('ongoing')"),
    'Upcoming events'      => (int) Database::value("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()"),
    'Active campaigns'     => (int) Database::value("SELECT COUNT(*) FROM campaigns WHERE is_active=1"),
    'Donations (pending)'  => (int) Database::value("SELECT COUNT(*) FROM donations WHERE status='pending'"),
    'Unread messages'      => (int) Database::value("SELECT COUNT(*) FROM contact_messages WHERE is_read=0"),
    'Volunteer applications' => (int) Database::value("SELECT COUNT(*) FROM volunteers WHERE status='pending'"),
];
$totalRaised = (float) Database::value("SELECT COALESCE(SUM(amount),0) FROM donations WHERE status='received'");
?>
<div class="row g-3 mb-4">
  <?php foreach ($stats as $label => $val): ?>
  <div class="col-6 col-md-3">
    <div class="stat-tile">
      <div class="num text-blue"><?= $val ?></div>
      <div class="small text-muted"><?= e($label) ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">Total donations received</h6>
      <div class="display-font fw-bold text-green fs-2"><?= format_inr($totalRaised) ?></div>
      <a href="<?= admin_url('index.php?page=donations') ?>" class="btn btn-outline-nav btn-sm mt-2">Manage donations</a>
    </div>
  </div>
  <div class="col-md-6">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">Quick links</h6>
      <div class="d-flex flex-wrap gap-2">
        <a href="<?= admin_url('index.php?page=members') ?>" class="btn btn-outline-nav btn-sm">Approve members</a>
        <a href="<?= admin_url('index.php?page=events') ?>" class="btn btn-outline-nav btn-sm">Manage events</a>
        <a href="<?= admin_url('index.php?page=contacts') ?>" class="btn btn-outline-nav btn-sm">Read messages</a>
        <a href="<?= admin_url('index.php?page=settings') ?>" class="btn btn-outline-nav btn-sm">Website settings</a>
      </div>
    </div>
  </div>
</div>
