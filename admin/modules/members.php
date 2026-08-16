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
        'member_no'  => $member['member_no'] ?: generate_member_no($member['district_id'] ?: null),
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
if ($action === 'notify' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $title = post('title');
    $message = post('message');
    if ($title === '' || $message === '') {
        flash_set('error', 'Notification title and message are both required.');
    } else {
        Database::insert('member_notifications', ['member_id' => $id, 'title' => $title, 'message' => $message]);
        flash_set('success', 'Notification sent.');
    }
    redirect('admin/index.php?page=members');
}

$statusFilter = get_param('status', 'pending');
$where = $statusFilter !== 'all' ? "m.status=?" : "1=1";
$params = $statusFilter !== 'all' ? [$statusFilter] : [];
$members = Database::all("SELECT m.*, c.name AS category_name,
                                  st.name AS state_name, d.name AS district_name, t.name AS taluka_name
                           FROM members m
                           LEFT JOIN membership_categories c ON c.id = m.category_id
                           LEFT JOIN states st ON st.id = m.state_id
                           LEFT JOIN districts d ON d.id = m.district_id
                           LEFT JOIN talukas t ON t.id = m.taluka_id
                           WHERE $where ORDER BY m.created_at DESC", $params);
$idProofLabels = id_proof_types();
?>
<ul class="nav nav-pills mb-4">
  <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $k => $label): ?>
    <li class="nav-item"><a class="nav-link <?= $statusFilter===$k?'active':'' ?>" href="<?= admin_url('index.php?page=members&status=' . $k) ?>"><?= $label ?></a></li>
  <?php endforeach; ?>
</ul>

<div class="admin-card">
  <div class="table-responsive">
    <table class="table table-admin align-middle">
      <thead><tr><th>Photo</th><th>Name</th><th>Member No.</th><th>Category</th><th>Email / Phone</th><th>Location</th><th>ID Proof</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($members as $m): ?>
        <tr>
          <td><?= $m['photo'] ? '<img src="'.e(upload_url($m['photo'])).'" class="thumb-sm">' : '<i class="fa-solid fa-user text-muted"></i>' ?></td>
          <td><?= e($m['name']) ?></td>
          <td><?= e($m['member_no'] ?: '—') ?></td>
          <td><?= e($m['category_name'] ?? '—') ?></td>
          <td class="small"><?= e($m['email']) ?><br><?= e($m['phone']) ?></td>
          <td class="small">
            <?= e($m['taluka_name'] ?: $m['taluka_other'] ?: '') ?><?= ($m['taluka_name'] || $m['taluka_other']) && ($m['district_name'] || $m['district_other']) ? ', ' : '' ?>
            <?= e($m['district_name'] ?: $m['district_other'] ?: '') ?><br>
            <span class="text-muted"><?= e($m['state_name'] ?? '—') ?><?= $m['pincode'] ? ' - ' . e($m['pincode']) : '' ?></span>
          </td>
          <td class="small">
            <?php if ($m['id_proof_type']): ?>
              <?= e($idProofLabels[$m['id_proof_type']] ?? $m['id_proof_type']) ?><br>
              <span class="text-muted"><?= e($m['id_proof_number']) ?></span><br>
              <?php if ($m['id_proof_file']): ?>
                <a href="<?= admin_url('download.php?type=id_proof&id=' . $m['id']) ?>" target="_blank" rel="noopener noreferrer">View document</a>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
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
            <button type="button" class="btn btn-sm btn-outline-nav" data-bs-toggle="modal" data-bs-target="#notifyModal-<?= $m['id'] ?>" title="Send notification"><i class="fa-solid fa-bell"></i></button>
            <form method="post" action="<?= admin_url('index.php?page=members&action=delete&id=' . $m['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this member record permanently?');"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-trash"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$members): ?><tr><td colspan="9" class="text-center text-muted py-4">No members in this filter.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($members as $m): ?>
<div class="modal fade" id="notifyModal-<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= admin_url('index.php?page=members&action=notify&id=' . $m['id']) ?>">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Send notification to <?= e($m['name']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="notify-edit">
            <div class="mb-3"><label class="form-label">Title</label><input class="form-control notify-title-input" name="title" maxlength="150" required></div>
            <div class="mb-3"><label class="form-label">Message</label><textarea class="form-control notify-message-input" name="message" rows="4" required></textarea></div>
          </div>
          <div class="notify-preview d-none">
            <p class="small text-muted mb-1">Preview - exactly what <?= e($m['name']) ?> will see on their dashboard:</p>
            <div class="border rounded p-3">
              <h6 class="fw-bold notify-preview-title"></h6>
              <p class="mb-0 notify-preview-message" style="white-space:pre-wrap;"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-nav btn-notify-preview">Preview</button>
          <button type="submit" class="btn btn-blue d-none btn-notify-confirm">Confirm &amp; Send</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script>
document.addEventListener('click', function (e) {
  var previewBtn = e.target.closest('.btn-notify-preview');
  if (!previewBtn) return;
  var modal = previewBtn.closest('.modal');
  var title = modal.querySelector('.notify-title-input').value.trim();
  var message = modal.querySelector('.notify-message-input').value.trim();
  if (!title || !message) return;
  modal.querySelector('.notify-preview-title').textContent = title;
  modal.querySelector('.notify-preview-message').textContent = message;
  modal.querySelector('.notify-edit').classList.add('d-none');
  modal.querySelector('.notify-preview').classList.remove('d-none');
  previewBtn.classList.add('d-none');
  modal.querySelector('.btn-notify-confirm').classList.remove('d-none');
});
</script>
