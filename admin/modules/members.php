<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if ($action === 'approve' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $member = Database::one("SELECT * FROM members WHERE id=?", [$id]);
    $category = $member['category_id'] ? Database::one("SELECT * FROM membership_categories WHERE id=?", [$member['category_id']]) : null;
    $months = $category['duration_months'] ?? 12;
    Database::update('members', [
        'status'     => 'approved',
        'member_no'  => $member['member_no'] ?: generate_member_no(),
        'valid_till' => date('Y-m-d', strtotime("+$months months")),
    ], 'id=?', [$id]);
    flash_set('success', 'Member approved and ID card enabled.');
    redirect('admin/index.php?page=members');
}
if ($action === 'reject' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::update('members', ['status' => 'rejected'], 'id=?', [$id]);
    flash_set('info', 'Member application rejected.');
    redirect('admin/index.php?page=members');
}
if ($action === 'delete' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    delete_upload(Database::value("SELECT photo FROM members WHERE id=?", [$id]));
    Database::delete('members', 'id=?', [$id]);
    flash_set('success', 'Member record deleted.');
    redirect('admin/index.php?page=members');
}

$statusFilter = get_param('status', 'pending');
$where = $statusFilter !== 'all' ? "status=?" : "1=1";
$params = $statusFilter !== 'all' ? [$statusFilter] : [];
$members = Database::all("SELECT m.*, c.name AS category_name FROM members m
                           LEFT JOIN membership_categories c ON c.id = m.category_id
                           WHERE $where ORDER BY m.created_at DESC", $params);
?>
<ul class="nav nav-pills mb-4">
  <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $k => $label): ?>
    <li class="nav-item"><a class="nav-link <?= $statusFilter===$k?'active':'' ?>" href="<?= admin_url('index.php?page=members&status=' . $k) ?>"><?= $label ?></a></li>
  <?php endforeach; ?>
</ul>

<div class="admin-card">
  <div class="table-responsive">
    <table class="table table-admin align-middle">
      <thead><tr><th>Photo</th><th>Name</th><th>Member No.</th><th>Category</th><th>Email / Phone</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($members as $m): ?>
        <tr>
          <td><?= $m['photo'] ? '<img src="'.e(upload_url($m['photo'])).'" class="thumb-sm">' : '<i class="fa-solid fa-user text-muted"></i>' ?></td>
          <td><?= e($m['name']) ?></td>
          <td><?= e($m['member_no'] ?: '—') ?></td>
          <td><?= e($m['category_name'] ?? '—') ?></td>
          <td class="small"><?= e($m['email']) ?><br><?= e($m['phone']) ?></td>
          <td>
            <?php $badge = ['approved'=>'badge-green','pending'=>'badge-orange','rejected'=>'bg-danger-subtle text-danger'][$m['status']]; ?>
            <span class="badge-type <?= $badge ?>"><?= e(ucfirst($m['status'])) ?></span>
          </td>
          <td class="text-end text-nowrap">
            <?php if ($m['status'] !== 'approved'): ?>
            <form method="post" action="<?= admin_url('index.php?page=members&action=approve&id=' . $m['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-green">Approve</button></form>
            <?php endif; ?>
            <?php if ($m['status'] !== 'rejected'): ?>
            <form method="post" action="<?= admin_url('index.php?page=members&action=reject&id=' . $m['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Reject</button></form>
            <?php endif; ?>
            <form method="post" action="<?= admin_url('index.php?page=members&action=delete&id=' . $m['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this member record permanently?');"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-trash"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$members): ?><tr><td colspan="7" class="text-center text-muted py-4">No members in this filter.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
