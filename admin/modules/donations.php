<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if ($action === 'mark_received' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $donation = Database::one("SELECT * FROM donations WHERE id=?", [$id]);
    if ($donation && $donation['status'] !== 'received') {
        Database::update('donations', ['status' => 'received'], 'id=?', [$id]);
        if (!empty($donation['campaign_id'])) {
            Database::query("UPDATE campaigns SET raised_amount = raised_amount + ? WHERE id=?", [$donation['amount'], $donation['campaign_id']]);
        }
        flash_set('success', 'Donation marked as received. Campaign total updated.');
    }
    redirect('admin/index.php?page=donations');
}
if ($action === 'mark_failed' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::update('donations', ['status' => 'failed'], 'id=?', [$id]);
    flash_set('info', 'Donation marked as failed.');
    redirect('admin/index.php?page=donations');
}

$statusFilter   = get_param('status', 'pending');
$campaignFilter = (int) get_param('campaign_id');

$conditions = [];
$params = [];
if ($statusFilter === 'crowdfunding') {
    $conditions[] = 'd.campaign_id IS NOT NULL';
    if ($campaignFilter) {
        $conditions[] = 'd.campaign_id = ?';
        $params[] = $campaignFilter;
    }
} elseif ($statusFilter !== 'all') {
    $conditions[] = 'd.status = ?';
    $params[] = $statusFilter;
}
$where = $conditions ? implode(' AND ', $conditions) : '1=1';

$donations = Database::all("SELECT d.*, c.title AS campaign_title FROM donations d
                             LEFT JOIN campaigns c ON c.id = d.campaign_id
                             WHERE $where ORDER BY d.created_at DESC", $params);
$totalReceived = (float) Database::value("SELECT COALESCE(SUM(amount),0) FROM donations WHERE status='received'");
$allCampaigns = Database::all("SELECT id, title FROM campaigns ORDER BY title");
?>
<div class="admin-card mb-3">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><span class="text-muted small">Total received</span><div class="fs-4 fw-bold text-green"><?= format_inr($totalReceived) ?></div></div>
    <ul class="nav nav-pills flex-wrap">
      <?php foreach (['pending'=>'Pending','received'=>'Received','failed'=>'Failed','crowdfunding'=>'Crowdfunding','all'=>'All'] as $k=>$label): ?>
        <li class="nav-item"><a class="nav-link <?= $statusFilter===$k?'active':'' ?>" href="<?= admin_url('index.php?page=donations&status=' . $k) ?>"><?= $label ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php if ($statusFilter === 'crowdfunding'): ?>
  <form method="get" class="mt-3 d-flex align-items-center gap-2">
    <input type="hidden" name="page" value="donations">
    <input type="hidden" name="status" value="crowdfunding">
    <label class="form-label small text-muted mb-0" for="campaign-filter">Program:</label>
    <select class="form-select form-select-sm w-auto" id="campaign-filter" name="campaign_id" onchange="this.form.submit()">
      <option value="">All crowdfunding programs</option>
      <?php foreach ($allCampaigns as $c): ?>
        <option value="<?= (int) $c['id'] ?>" <?= $campaignFilter === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
  <?php endif; ?>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="table table-admin align-middle">
      <thead><tr><th>Receipt</th><th>Donor</th><th>Campaign</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($donations as $d): ?>
        <tr>
          <td><code><?= e($d['receipt_no']) ?></code></td>
          <td><?= e($d['donor_name']) ?><br><span class="small text-muted"><?= e($d['email']) ?></span></td>
          <td><?= e($d['campaign_title'] ?? 'General fund') ?></td>
          <td class="fw-bold"><?= format_inr($d['amount']) ?></td>
          <td><?= e(strtoupper($d['method'])) ?></td>
          <td>
            <?php $badge = ['received'=>'badge-green','pending'=>'badge-orange','failed'=>'bg-danger-subtle text-danger'][$d['status']]; ?>
            <span class="badge-type <?= $badge ?>"><?= e(ucfirst($d['status'])) ?></span>
          </td>
          <td class="small"><?= format_date($d['created_at']) ?></td>
          <td class="text-end text-nowrap">
            <?php if ($d['status'] === 'pending'): ?>
            <form method="post" action="<?= admin_url('index.php?page=donations&action=mark_received&id=' . $d['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-green">Mark received</button></form>
            <form method="post" action="<?= admin_url('index.php?page=donations&action=mark_failed&id=' . $d['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Failed</button></form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$donations): ?><tr><td colspan="8" class="text-center text-muted py-4">No donations in this filter.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
