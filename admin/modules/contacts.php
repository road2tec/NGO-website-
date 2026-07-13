<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if ($action === 'read' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::update('contact_messages', ['is_read' => 1], 'id=?', [$id]);
    redirect('admin/index.php?page=contacts');
}
if ($action === 'delete' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::delete('contact_messages', 'id=?', [$id]);
    flash_set('success', 'Message deleted.');
    redirect('admin/index.php?page=contacts');
}

$rows = Database::all("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<div class="row g-3">
  <?php foreach ($rows as $m): ?>
  <div class="col-12">
    <div class="admin-card <?= $m['is_read'] ? '' : 'border-start border-4 border-primary' ?>">
      <div class="d-flex justify-content-between flex-wrap">
        <div>
          <strong><?= e($m['name']) ?></strong> <span class="text-muted small">&lt;<?= e($m['email']) ?>&gt; <?= e($m['phone']) ?></span>
          <span class="badge-type badge-blue ms-2"><?= e($m['source'] === 'homepage' ? 'Homepage enquiry' : 'Contact form') ?></span>
        </div>
        <span class="small text-muted"><?= format_date($m['created_at'], 'd M Y, H:i') ?></span>
      </div>
      <?php if ($m['subject']): ?><div class="fw-bold mt-2"><?= e($m['subject']) ?></div><?php endif; ?>
      <p class="text-muted mb-2 mt-1"><?= nl2br(e($m['message'])) ?></p>
      <div class="d-flex gap-2">
        <?php if (!$m['is_read']): ?>
        <form method="post" action="<?= admin_url('index.php?page=contacts&action=read&id=' . $m['id']) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-nav">Mark as read</button></form>
        <?php endif; ?>
        <form method="post" action="<?= admin_url('index.php?page=contacts&action=delete&id=' . $m['id']) ?>" onsubmit="return confirm('Delete this message?');"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Delete</button></form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (!$rows): ?><div class="col-12 text-center text-muted py-5">No messages yet.</div><?php endif; ?>
</div>
