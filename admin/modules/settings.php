<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $keys = [
        'site_name','site_tagline','site_email','site_phone','site_whatsapp','site_address','map_embed',
        'facebook_url','instagram_url','twitter_url','youtube_url',
        'seo_title','seo_description','seo_keywords',
        'donate_upi','registration_no','pan_80g','membership_fee_note','announcement',
        'bank_account_name','bank_name','bank_account_number','bank_ifsc','bank_branch',
        'crowdfunding_banner_title','crowdfunding_banner_text','crowdfunding_banner_campaign_id',
        'stat_members','stat_projects','stat_beneficiaries','stat_villages','member_no_prefix',
    ];
    foreach ($keys as $k) {
        save_setting($k, post($k));
    }
    if (!empty($_FILES['donate_qr_image']['name'])) {
        try {
            $img = handle_upload('donate_qr_image', 'misc');
            if ($img) save_setting('donate_qr_image', $img);
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
      <div class="admin-card">
        <h6 class="fw-bold mb-3">Social links</h6>
        <div class="mb-3"><label class="form-label">Facebook</label><input class="form-control" name="facebook_url" value="<?= e($s('facebook_url')) ?>"></div>
        <div class="mb-3"><label class="form-label">Instagram</label><input class="form-control" name="instagram_url" value="<?= e($s('instagram_url')) ?>"></div>
        <div class="mb-3"><label class="form-label">X (Twitter)</label><input class="form-control" name="twitter_url" value="<?= e($s('twitter_url')) ?>"></div>
        <div class="mb-3"><label class="form-label">YouTube</label><input class="form-control" name="youtube_url" value="<?= e($s('youtube_url')) ?>"></div>
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
