<?php
if (get_param('action') === 'export') {
    require_csrf();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter_subscribers.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Email', 'Subscribed On']);
    foreach (Database::all("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC") as $s) {
        fputcsv($out, [$s['email'], $s['created_at']]);
    }
    fclose($out);
    exit;
}
if (get_param('action') === 'delete' && get_param('id')) {
    require_csrf();
    Database::delete('newsletter_subscribers', 'id=?', [(int) get_param('id')]);
    redirect('admin/index.php?page=newsletter');
}

$rows = Database::all("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC");
?>
<div class="admin-card">
  <div class="d-flex justify-content-between mb-3">
    <h6 class="fw-bold mb-0">Subscribers (<?= count($rows) ?>)</h6>
    <form method="post" action="<?= admin_url('index.php?page=newsletter&action=export') ?>"><?= csrf_field() ?><button class="btn btn-outline-nav btn-sm"><i class="fa-solid fa-download me-1"></i>Export CSV</button></form>
  </div>
  <div class="table-responsive">
    <table class="table table-admin">
      <thead><tr><th>Email</th><th>Subscribed on</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $s): ?>
        <tr>
          <td><?= e($s['email']) ?></td><td><?= format_date($s['created_at']) ?></td>
          <td class="text-end">
            <form method="post" action="<?= admin_url('index.php?page=newsletter&action=delete&id=' . $s['id']) ?>" onsubmit="return confirm('Remove subscriber?');"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="3" class="text-center text-muted py-4">No subscribers yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
