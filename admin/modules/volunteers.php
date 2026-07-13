<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if (in_array($action, ['approve', 'reject'], true) && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::update('volunteers', ['status' => $action === 'approve' ? 'approved' : 'rejected'], 'id=?', [$id]);
    flash_set('success', 'Volunteer application updated.');
    redirect('admin/index.php?page=volunteers');
}
if ($action === 'delete' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    delete_upload(Database::value("SELECT resume FROM volunteers WHERE id=?", [$id]));
    Database::delete('volunteers', 'id=?', [$id]);
    flash_set('success', 'Application deleted.');
    redirect('admin/index.php?page=volunteers');
}

$rows = Database::all("SELECT * FROM volunteers ORDER BY created_at DESC");
?>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table table-admin align-middle">
      <thead><tr><th>Name</th><th>Contact</th><th>City</th><th>Availability</th><th>Resume</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $v): ?>
        <tr>
          <td><?= e($v['name']) ?></td>
          <td class="small"><?= e($v['email']) ?><br><?= e($v['phone']) ?></td>
          <td><?= e($v['city']) ?></td>
          <td><?= e($v['availability']) ?></td>
          <td><?= $v['resume'] ? '<a href="'.e(upload_url($v['resume'])).'" target="_blank" rel="noopener">View</a>' : '—' ?></td>
          <td>
            <?php $badge = ['approved'=>'badge-green','pending'=>'badge-orange','rejected'=>'bg-danger-subtle text-danger'][$v['status']]; ?>
            <span class="badge-type <?= $badge ?>"><?= e(ucfirst($v['status'])) ?></span>
          </td>
          <td class="text-end text-nowrap">
            <?php if ($v['status'] !== 'approved'): ?><form method="post" action="<?= admin_url('index.php?page=volunteers&action=approve&id=' . $v['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-green">Approve</button></form><?php endif; ?>
            <?php if ($v['status'] !== 'rejected'): ?><form method="post" action="<?= admin_url('index.php?page=volunteers&action=reject&id=' . $v['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Reject</button></form><?php endif; ?>
            <form method="post" action="<?= admin_url('index.php?page=volunteers&action=delete&id=' . $v['id']) ?>" class="d-inline" onsubmit="return confirm('Delete?');"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-trash"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7" class="text-center text-muted py-4">No volunteer applications yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
