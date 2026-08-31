<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'send_test_email') {
    require_csrf();
    $testTo = post('test_email') ?: setting('site_email');
    if (!valid_email($testTo)) {
        flash_set('error', 'Enter a valid email address to send the test to.');
    } else {
        $attachments = [];
        $body = "This is a test email confirming your SMTP settings are working.\n\nSent at " . date('d M Y, H:i');
        if (post('include_certificate')) {
            try {
                $sample = [
                    'id' => 0, 'receipt_no' => 'RCPT-SAMPLE-0001', 'cert_code' => 'SAMPLE0001',
                    'donor_name' => 'Sample Donor', 'email' => $testTo, 'phone' => '+91 90000 00000',
                    'amount' => 2500, 'method' => 'upi', 'txn_ref' => 'SAMPLE-TXN-001',
                    'campaign_title' => null, 'created_at' => date('Y-m-d'),
                ];
                $attachments = [
                    ['name' => 'sample-certificate.pdf', 'content' => generate_donation_certificate_pdf($sample)],
                    ['name' => 'sample-receipt.pdf', 'content' => generate_donation_receipt_pdf($sample)],
                ];
                $body .= "\n\nThis test also includes a sample donation certificate and receipt (using placeholder data) as PDF attachments, to confirm PDF generation works end to end.";
            } catch (RuntimeException $e) {
                flash_set('error', $e->getMessage());
                redirect('admin/index.php?page=settings');
            }
        }
        if (send_mail($testTo, 'Test email from ' . setting('site_name'), $body, null, $attachments)) {
            flash_set('success', "Test email sent to $testTo" . ($attachments ? ' with sample certificate + receipt attached' : '') . '. Check the inbox (and spam folder).');
        } else {
            flash_set('error', 'Could not send the test email. Double-check the SMTP host, port, username and password below.');
        }
    }
    redirect('admin/index.php?page=settings');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') !== 'send_test_email') {
    require_csrf();
    $keys = [
        'site_name','site_tagline','site_email','site_phone','site_whatsapp','site_address','map_embed',
        'facebook_url','instagram_url','twitter_url','youtube_url',
        'seo_title','seo_description','seo_keywords',
        'donate_upi','registration_no','pan_80g','membership_fee_note','announcement',
        'bank_account_name','bank_name','bank_account_number','bank_ifsc','bank_branch',
        'crowdfunding_banner_title','crowdfunding_banner_text','crowdfunding_banner_campaign_id',
        'stat_members','stat_projects','stat_beneficiaries','stat_villages','member_no_prefix',
        'smtp_host','smtp_port','smtp_username','smtp_encryption','smtp_from_email','smtp_from_name',
        'cert_message_template','failed_payment_email_template',
        'org_legal_status','org_pan','org_80g_urn','org_website',
        'cert_signatory_name','cert_signatory_designation','membership_benefits',
    ];
    foreach ($keys as $k) {
        save_setting($k, post($k));
    }
    // Leave the stored SMTP password untouched when the field is left blank,
    // so re-saving the rest of the form doesn't wipe out working credentials.
    if (post('smtp_password') !== '') {
        save_setting('smtp_password', post('smtp_password'));
    }
    if (!empty($_FILES['donate_qr_image']['name'])) {
        try {
            $img = handle_upload('donate_qr_image', 'misc');
            if ($img) save_setting('donate_qr_image', $img);
        } catch (RuntimeException $e) { flash_set('error', $e->getMessage()); }
    }
    if (!empty($_FILES['org_logo']['name'])) {
        try {
            $img = handle_upload('org_logo', 'misc');
            if ($img) save_setting('org_logo', $img);
        } catch (RuntimeException $e) { flash_set('error', $e->getMessage()); }
    }
    if (!empty($_FILES['cert_signature_image']['name'])) {
        try {
            $img = handle_upload('cert_signature_image', 'misc');
            if ($img) save_setting('cert_signature_image', $img);
        } catch (RuntimeException $e) { flash_set('error', $e->getMessage()); }
    }
    flash_set('success', 'Settings saved.');
    redirect('admin/index.php?page=settings');
}

$s = fn($k) => setting($k);
$activeCampaigns = Database::all("SELECT id, title FROM campaigns WHERE is_active=1 ORDER BY title");
?>
<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="admin-card mb-4">
        <h6 class="fw-bold mb-3">General</h6>
        <div class="mb-3"><label class="form-label">Site name</label><input class="form-control" name="site_name" value="<?= e($s('site_name')) ?>"></div>
        <div class="mb-3"><label class="form-label">Tagline</label><input class="form-control" name="site_tagline" value="<?= e($s('site_tagline')) ?>"></div>
        <div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="site_email" value="<?= e($s('site_email')) ?>"></div>
        <div class="mb-3"><label class="form-label">Phone</label><input class="form-control" name="site_phone" value="<?= e($s('site_phone')) ?>"></div>
        <div class="mb-3"><label class="form-label">WhatsApp</label><input class="form-control" name="site_whatsapp" value="<?= e($s('site_whatsapp')) ?>"></div>
        <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" name="site_address" rows="2"><?= e($s('site_address')) ?></textarea></div>
        <div class="mb-3"><label class="form-label">Google Map embed code</label><textarea class="form-control" name="map_embed" rows="3"><?= e($s('map_embed')) ?></textarea></div>
      </div>
      <div class="admin-card mb-4">
        <h6 class="fw-bold mb-3">Social links</h6>
        <div class="mb-3"><label class="form-label">Facebook</label><input class="form-control" name="facebook_url" value="<?= e($s('facebook_url')) ?>"></div>
        <div class="mb-3"><label class="form-label">Instagram</label><input class="form-control" name="instagram_url" value="<?= e($s('instagram_url')) ?>"></div>
        <div class="mb-3"><label class="form-label">X (Twitter)</label><input class="form-control" name="twitter_url" value="<?= e($s('twitter_url')) ?>"></div>
        <div class="mb-3"><label class="form-label">YouTube</label><input class="form-control" name="youtube_url" value="<?= e($s('youtube_url')) ?>"></div>
      </div>
      <div class="admin-card">
        <h6 class="fw-bold mb-3">Email (SMTP)</h6>
        <p class="small text-muted">Used for password-reset links, the contact-form notification and donation confirmations. Without this configured, the site falls back to PHP's built-in <code>mail()</code>, which most hosts (including Hostinger shared hosting) block or silently drop - fill this in with a real mailbox's SMTP details (e.g. hPanel &rarr; Emails &rarr; Connect Devices) to make email actually deliver.</p>
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">SMTP host</label><input class="form-control" name="smtp_host" value="<?= e($s('smtp_host')) ?>" placeholder="smtp.hostinger.com"></div>
          <div class="col-md-4"><label class="form-label">Port</label><input class="form-control" name="smtp_port" value="<?= e($s('smtp_port') ?: '587') ?>"></div>
          <div class="col-md-6"><label class="form-label">Encryption</label>
            <select class="form-select" name="smtp_encryption">
              <?php foreach (['tls' => 'TLS (587)', 'ssl' => 'SSL (465)'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= $s('smtp_encryption') === $val ? 'selected' : '' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="smtp_username" value="<?= e($s('smtp_username')) ?>" placeholder="notifications@yourdomain.org"></div>
          <div class="col-12">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="smtp_password" placeholder="<?= $s('smtp_password') ? 'Leave blank to keep the current password' : 'SMTP password' ?>" autocomplete="new-password">
          </div>
          <div class="col-md-6"><label class="form-label">From name</label><input class="form-control" name="smtp_from_name" value="<?= e($s('smtp_from_name')) ?>" placeholder="<?= e($s('site_name')) ?>"></div>
          <div class="col-md-6"><label class="form-label">From email</label><input class="form-control" name="smtp_from_email" value="<?= e($s('smtp_from_email')) ?>" placeholder="<?= e($s('site_email')) ?>"></div>
        </div>
      </div>
      <div class="admin-card mt-4">
        <h6 class="fw-bold mb-3">Certificate &amp; email templates</h6>
        <p class="small text-muted">Saved here once, reused for every future certificate/email. You can still tweak the wording for a single send from the Donations page without changing this default.</p>
        <div class="mb-3">
          <label class="form-label">Certificate appreciation message</label>
          <textarea class="form-control" name="cert_message_template" rows="3"><?= e($s('cert_message_template') ?: default_cert_message_template()) ?></textarea>
          <div class="form-text">Placeholders: <code>{{donor_name}}</code> <code>{{amount}}</code> <code>{{campaign}}</code> <code>{{date}}</code> <code>{{receipt_no}}</code> <code>{{site_name}}</code></div>
        </div>
        <div class="mb-1">
          <label class="form-label">Failed-payment email</label>
          <textarea class="form-control" name="failed_payment_email_template" rows="6"><?= e($s('failed_payment_email_template') ?: default_failed_payment_template()) ?></textarea>
          <div class="form-text">Sent when admin marks a donation "Failed" on the Donations page. Same placeholders as above.</div>
        </div>
      </div>
      <div class="admin-card mt-4">
        <h6 class="fw-bold mb-3">Certificate &amp; receipt branding</h6>
        <p class="small text-muted">Logo, signature and legal details printed on every donation certificate and receipt PDF. Edit and save here once - it applies to all future certificates/receipts automatically.</p>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Organisation logo</label>
            <?php if ($s('org_logo')): ?><div class="small text-muted mb-1"><img src="<?= e(upload_url($s('org_logo'))) ?>" class="thumb-sm mb-1"></div><?php endif; ?>
            <input type="file" class="form-control" name="org_logo" accept="image/*">
          </div>
          <div class="col-md-6">
            <label class="form-label">Signature image</label>
            <?php if ($s('cert_signature_image')): ?><div class="small text-muted mb-1"><img src="<?= e(upload_url($s('cert_signature_image'))) ?>" class="thumb-sm mb-1"></div><?php endif; ?>
            <input type="file" class="form-control" name="cert_signature_image" accept="image/*">
          </div>
          <div class="col-md-6"><label class="form-label">Signatory name</label><input class="form-control" name="cert_signatory_name" value="<?= e($s('cert_signatory_name')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Signatory designation</label><input class="form-control" name="cert_signatory_designation" value="<?= e($s('cert_signatory_designation')) ?>" placeholder="e.g. Founder & Trustee"></div>
          <div class="col-md-6"><label class="form-label">Legal status line</label><input class="form-control" name="org_legal_status" value="<?= e($s('org_legal_status') ?: 'Section 8 Company / Registered NGO') ?>"></div>
          <div class="col-md-6"><label class="form-label">Website (shown on ID cards)</label><input class="form-control" name="org_website" value="<?= e($s('org_website')) ?>" placeholder="<?= e(BASE_URL) ?>"></div>
          <div class="col-md-6"><label class="form-label">Organisation PAN</label><input class="form-control" name="org_pan" value="<?= e($s('org_pan')) ?>"></div>
          <div class="col-md-6"><label class="form-label">80G Approval / URN</label><input class="form-control" name="org_80g_urn" value="<?= e($s('org_80g_urn')) ?>"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="admin-card mb-4">
        <h6 class="fw-bold mb-3">SEO</h6>
        <div class="mb-3"><label class="form-label">SEO title</label><input class="form-control" name="seo_title" value="<?= e($s('seo_title')) ?>"></div>
        <div class="mb-3"><label class="form-label">Meta description</label><textarea class="form-control" name="seo_description" rows="2"><?= e($s('seo_description')) ?></textarea></div>
        <div class="mb-3"><label class="form-label">Meta keywords</label><input class="form-control" name="seo_keywords" value="<?= e($s('seo_keywords')) ?>"></div>
      </div>
      <div class="admin-card mb-4">
        <h6 class="fw-bold mb-3">Donations &amp; legal</h6>
        <div class="mb-3"><label class="form-label">UPI ID</label><input class="form-control" name="donate_upi" value="<?= e($s('donate_upi')) ?>"></div>
        <div class="mb-3"><label class="form-label">Donation QR image (optional)</label><input type="file" class="form-control" name="donate_qr_image" accept="image/*"></div>
        <div class="mb-3"><label class="form-label">Registration No.</label><input class="form-control" name="registration_no" value="<?= e($s('registration_no')) ?>"></div>
        <div class="mb-3"><label class="form-label">80G / 12A note</label><input class="form-control" name="pan_80g" value="<?= e($s('pan_80g')) ?>"></div>
        <div class="mb-3"><label class="form-label">Membership fee note</label><input class="form-control" name="membership_fee_note" value="<?= e($s('membership_fee_note')) ?>"></div>
        <div class="mb-3"><label class="form-label">Member number prefix</label><input class="form-control" name="member_no_prefix" value="<?= e($s('member_no_prefix') ?: 'MEM') ?>" maxlength="10"><div class="form-text">Used when a member is approved, e.g. <code><?= e($s('member_no_prefix') ?: 'MEM') ?>-PUN-26-0001</code>.</div></div>
        <div class="mb-3">
          <label class="form-label">Membership benefits (shown on the back of the ID card)</label>
          <textarea class="form-control" name="membership_benefits" rows="4"><?= e($s('membership_benefits')) ?></textarea>
          <div class="form-text">One benefit per line.</div>
        </div>
        <div class="mb-3"><label class="form-label">Homepage announcement banner</label><input class="form-control" name="announcement" value="<?= e($s('announcement')) ?>"></div>
      </div>
      <div class="admin-card mb-4">
        <h6 class="fw-bold mb-3">Bank transfer details</h6>
        <p class="small text-muted">Shown on the Donate page with one-click copy buttons for Account Number and IFSC.</p>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Account Name</label><input class="form-control" name="bank_account_name" value="<?= e($s('bank_account_name')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Bank Name</label><input class="form-control" name="bank_name" value="<?= e($s('bank_name')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Account Number</label><input class="form-control" name="bank_account_number" value="<?= e($s('bank_account_number')) ?>"></div>
          <div class="col-md-6"><label class="form-label">IFSC Code</label><input class="form-control" name="bank_ifsc" value="<?= e($s('bank_ifsc')) ?>"></div>
          <div class="col-md-12"><label class="form-label">Branch (optional)</label><input class="form-control" name="bank_branch" value="<?= e($s('bank_branch')) ?>"></div>
        </div>
      </div>
      <div class="admin-card mb-4">
        <h6 class="fw-bold mb-3">Homepage crowdfunding banner</h6>
        <div class="mb-3"><label class="form-label">Headline</label><input class="form-control" name="crowdfunding_banner_title" value="<?= e($s('crowdfunding_banner_title')) ?>"></div>
        <div class="mb-3"><label class="form-label">Supporting text</label><input class="form-control" name="crowdfunding_banner_text" value="<?= e($s('crowdfunding_banner_text')) ?>"></div>
        <div class="mb-3">
          <label class="form-label">Featured campaign (progress bar)</label>
          <select class="form-select" name="crowdfunding_banner_campaign_id">
            <option value="">Most recent active campaign (default)</option>
            <?php foreach ($activeCampaigns as $c): ?>
              <option value="<?= (int) $c['id'] ?>" <?= (string) $s('crowdfunding_banner_campaign_id') === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="admin-card">
        <h6 class="fw-bold mb-3">Homepage statistics</h6>
        <div class="row g-3">
          <div class="col-6"><label class="form-label">Members</label><input type="number" class="form-control" name="stat_members" value="<?= e($s('stat_members')) ?>"></div>
          <div class="col-6"><label class="form-label">Projects</label><input type="number" class="form-control" name="stat_projects" value="<?= e($s('stat_projects')) ?>"></div>
          <div class="col-6"><label class="form-label">Beneficiaries</label><input type="number" class="form-control" name="stat_beneficiaries" value="<?= e($s('stat_beneficiaries')) ?>"></div>
          <div class="col-6"><label class="form-label">Villages</label><input type="number" class="form-control" name="stat_villages" value="<?= e($s('stat_villages')) ?>"></div>
        </div>
      </div>
    </div>
  </div>
  <button class="btn btn-blue btn-lg mt-4" type="submit">Save settings</button>
</form>

<div class="admin-card mt-4" style="max-width: 560px;">
  <h6 class="fw-bold mb-3">Test email delivery</h6>
  <p class="small text-muted">Save your SMTP settings above first, then send a test to confirm they actually work end to end - no real donation needed.</p>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="do" value="send_test_email">
    <div class="d-flex gap-2 flex-wrap mb-2">
      <input type="email" class="form-control" name="test_email" placeholder="<?= e($s('site_email') ?: 'you@example.org') ?>" style="max-width: 320px;">
      <button class="btn btn-outline-nav" type="submit">Send test email</button>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="test-cert" name="include_certificate" value="1">
      <label class="form-check-label small" for="test-cert">Also attach a sample certificate + receipt PDF (using placeholder data) - tests PDF generation together with SMTP in one go</label>
    </div>
  </form>
</div>
