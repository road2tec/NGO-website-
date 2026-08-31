<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

function _fetch_donation_with_campaign(int $id): ?array
{
    return Database::one(
        "SELECT d.*, c.title AS campaign_title FROM donations d
         LEFT JOIN campaigns c ON c.id = d.campaign_id WHERE d.id=?", [$id]
    );
}

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

if ($action === 'send_certificate' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $donation = _fetch_donation_with_campaign($id);
    if (!$donation || $donation['status'] !== 'received') {
        flash_set('error', 'Only donations marked "received" can be sent a certificate.');
    } elseif (!$donation['email']) {
        flash_set('error', 'This donor has no email address on file.');
    } else {
        if (empty($donation['cert_code'])) {
            $donation['cert_code'] = generate_cert_code();
            Database::update('donations', ['cert_code' => $donation['cert_code']], 'id=?', [$id]);
        }
        $message = trim(post('message')) ?: null;
        if (post('save_template') && $message) {
            save_setting('cert_message_template', $message);
        }
        try {
            $sent = send_mail(
                $donation['email'],
                'Your donation certificate & receipt - ' . setting('site_name'),
                "Dear " . $donation['donor_name'] . ",\n\n"
                . "Thank you again for your donation. Please find your donation certificate and payment receipt "
                . "attached for your records and tax filing.\n\n"
                . "With gratitude,\n" . setting('site_name'),
                $donation['donor_name'],
                [
                    ['name' => 'certificate-' . $donation['receipt_no'] . '.pdf', 'content' => generate_donation_certificate_pdf($donation, $message)],
                    ['name' => 'receipt-' . $donation['receipt_no'] . '.pdf', 'content' => generate_donation_receipt_pdf($donation)],
                ]
            );
            if ($sent) {
                Database::update('donations', ['cert_sent_at' => date('Y-m-d H:i:s')], 'id=?', [$id]);
                flash_set('success', 'Certificate and receipt emailed to ' . $donation['email'] . '.');
            } else {
                flash_set('error', 'Could not send the email. Check the SMTP settings under Admin -> Settings -> Email.');
            }
        } catch (RuntimeException $e) {
            flash_set('error', $e->getMessage());
        }
    }
    redirect('admin/index.php?page=donations');
}

if ($action === 'send_failed' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $donation = _fetch_donation_with_campaign($id);
    if (!$donation) {
        flash_set('error', 'Donation not found.');
        redirect('admin/index.php?page=donations');
    }
    $message = trim(post('message'));
    if ($message === '') {
        flash_set('error', 'The email message cannot be empty.');
        redirect('admin/index.php?page=donations&action=compose_failed&id=' . $id);
    }
    if (post('save_template')) {
        save_setting('failed_payment_email_template', $message);
    }
    Database::update('donations', ['status' => 'failed'], 'id=?', [$id]);
    if ($donation['email']) {
        $sent = send_mail($donation['email'], 'About your donation pledge - ' . setting('site_name'), $message, $donation['donor_name']);
        flash_set($sent ? 'info' : 'error', $sent
            ? 'Donation marked as failed and the email was sent to ' . $donation['email'] . '.'
            : 'Donation marked as failed, but the email could not be sent. Check the SMTP settings under Admin -> Settings -> Email.');
    } else {
        flash_set('info', 'Donation marked as failed. No email address on file, so no email was sent.');
    }
    redirect('admin/index.php?page=donations');
}

if ($action === 'compose_certificate' && $id) {
    $donation = _fetch_donation_with_campaign($id);
    if (!$donation || $donation['status'] !== 'received') { flash_set('error', 'Only donations marked "received" can be sent a certificate.'); redirect('admin/index.php?page=donations'); }
    $message = render_template(setting('cert_message_template') ?: default_cert_message_template(), donation_template_vars($donation));
    ?>
    <div class="admin-card" style="max-width:720px;">
      <h6 class="fw-bold mb-1">Send certificate &amp; receipt</h6>
      <p class="small text-muted">To <?= e($donation['donor_name']) ?> &lt;<?= e($donation['email']) ?>&gt; - <?= format_inr($donation['amount']) ?> for <?= e($donation['campaign_title'] ?? 'General Fund') ?></p>
      <form method="post" action="<?= admin_url('index.php?page=donations&action=send_certificate&id=' . $id) ?>" onsubmit="return confirm('Send the certificate and receipt to <?= e($donation['email']) ?>?');">
        <?= csrf_field() ?>
        <label class="form-label">Certificate appreciation message</label>
        <textarea class="form-control mb-2" name="message" rows="4"><?= e($message) ?></textarea>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="cf-save" name="save_template" value="1">
          <label class="form-check-label small" for="cf-save">Save this wording as the default template for future certificates</label>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-outline-nav" href="<?= admin_url('download.php?type=donation_certificate&id=' . $id) ?>" target="_blank" rel="noopener noreferrer">Preview last-saved version</a>
          <button class="btn btn-blue" type="submit">Confirm &amp; send certificate + receipt</button>
          <a class="btn btn-outline-danger" href="<?= admin_url('index.php?page=donations') ?>">Cancel</a>
        </div>
        <p class="small text-muted mt-2 mb-0">The preview link shows the currently-saved template - edit and save the wording (or check the box above) first if you want the preview to match exactly.</p>
      </form>
    </div>
    <?php
    return;
}

if ($action === 'compose_failed' && $id) {
    $donation = _fetch_donation_with_campaign($id);
    if (!$donation) { flash_set('error', 'Donation not found.'); redirect('admin/index.php?page=donations'); }
    $message = render_template(setting('failed_payment_email_template') ?: default_failed_payment_template(), donation_template_vars($donation));
    ?>
    <div class="admin-card" style="max-width:720px;">
      <h6 class="fw-bold mb-1">Mark as failed &amp; notify donor</h6>
      <p class="small text-muted">
        To <?= $donation['email'] ? e($donation['donor_name']) . ' &lt;' . e($donation['email']) . '&gt;' : e($donation['donor_name']) . ' (no email on file - status will still be updated)' ?>
        - <?= format_inr($donation['amount']) ?> for <?= e($donation['campaign_title'] ?? 'General Fund') ?>
      </p>
      <form method="post" action="<?= admin_url('index.php?page=donations&action=send_failed&id=' . $id) ?>" onsubmit="return confirm('Mark this donation as failed<?= $donation['email'] ? ' and email ' . e($donation['email']) : '' ?>?');">
        <?= csrf_field() ?>
        <label class="form-label">Message<?= $donation['email'] ? ' to donor' : '' ?></label>
        <textarea class="form-control mb-2" name="message" rows="8"><?= e($message) ?></textarea>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="ff-save" name="save_template" value="1">
          <label class="form-check-label small" for="ff-save">Save this wording as the default template for future failed-payment emails</label>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-outline-danger" type="submit">Confirm &amp; mark failed<?= $donation['email'] ? ' + send email' : '' ?></button>
          <a class="btn btn-outline-nav" href="<?= admin_url('index.php?page=donations') ?>">Cancel</a>
        </div>
      </form>
    </div>
    <?php
    return;
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
            <div class="d-flex gap-1 justify-content-end flex-wrap">
              <form method="post" action="<?= admin_url('index.php?page=donations&action=mark_received&id=' . $d['id']) ?>" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-green">Mark received</button></form>
              <a class="btn btn-sm btn-outline-danger" href="<?= admin_url('index.php?page=donations&action=compose_failed&id=' . $d['id']) ?>">Failed</a>
            </div>
            <?php elseif ($d['status'] === 'received'): ?>
            <div class="d-flex gap-1 justify-content-end flex-wrap align-items-center">
              <a class="btn btn-sm btn-outline-nav" href="<?= admin_url('download.php?type=donation_certificate&id=' . $d['id']) ?>" target="_blank" rel="noopener noreferrer">Preview certificate</a>
              <a class="btn btn-sm btn-outline-nav" title="Download certificate PDF" href="<?= admin_url('download.php?type=donation_certificate&id=' . $d['id'] . '&download=1') ?>"><i class="fa-solid fa-download"></i></a>
              <a class="btn btn-sm btn-outline-nav" href="<?= admin_url('download.php?type=donation_receipt&id=' . $d['id']) ?>" target="_blank" rel="noopener noreferrer">Preview receipt</a>
              <a class="btn btn-sm btn-outline-nav" title="Download receipt PDF" href="<?= admin_url('download.php?type=donation_receipt&id=' . $d['id'] . '&download=1') ?>"><i class="fa-solid fa-download"></i></a>
              <a class="btn btn-sm btn-blue" href="<?= admin_url('index.php?page=donations&action=compose_certificate&id=' . $d['id']) ?>"><?= $d['cert_sent_at'] ? 'Resend' : 'Send' ?> certificate &amp; receipt</a>
            </div>
            <?php if ($d['cert_sent_at']): ?><div class="small text-muted mt-1">Sent <?= format_date($d['cert_sent_at'], 'd M Y, H:i') ?></div><?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$donations): ?><tr><td colspan="8" class="text-center text-muted py-4">No donations in this filter.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
