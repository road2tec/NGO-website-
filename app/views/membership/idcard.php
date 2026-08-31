<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Member ID Card - <?= e($member['name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
  body { background:#eef3f9; padding:2rem 1rem; }
  .actions { max-width:340px; margin:1.4rem auto 0; }
  .id-card { -webkit-print-color-adjust: exact; print-color-adjust: exact; margin-bottom: 1.5rem; }
  .id-card .id-body { padding: 1rem 1.1rem; }
  .id-card .id-logo { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; background: #fff; padding: 3px; }
  .id-card .id-org-name { font-weight: 800; font-size: .95rem; letter-spacing: .02em; }
  .id-card .id-subtitle { font-size: .68rem; letter-spacing: .12em; text-transform: uppercase; opacity: .85; }
  .id-card .person-photo { width: 90px; height: 90px; margin: .3rem auto .5rem; }
  .id-card table.id-fields { width: 100%; font-size: .82rem; }
  .id-card table.id-fields th { color: var(--muted); font-weight: 600; white-space: nowrap; padding: .2rem .5rem .2rem 0; text-align: left; vertical-align: top; }
  .id-card table.id-fields td { font-weight: 700; padding: .2rem 0; }
  .id-card .id-sign { text-align: center; padding: .6rem 1rem .9rem; }
  .id-card .id-sign .line { border-top: 1px solid #cfd8e3; width: 70%; margin: 0 auto .3rem; padding-top: .3rem; font-size: .72rem; font-weight: 700; }
  .id-card .id-sign .org { font-size: .68rem; color: var(--muted); }
  .back-mission h6 { font-size: .78rem; text-transform: uppercase; letter-spacing: .08em; color: var(--blue); font-weight: 800; margin-bottom: .3rem; }
  .back-mission p, .back-mission li { font-size: .78rem; color: var(--ink); }
  .back-mission ul { padding-left: 1.1rem; margin-bottom: .8rem; }
  .back-notice { font-size: .72rem; color: var(--muted); border-top: 1px dashed #cfd8e3; padding-top: .6rem; margin-top: .4rem; }
  .back-contact { font-size: .74rem; }
  .back-contact div { margin-bottom: .15rem; }
  @media print {
    body { background: #fff; padding: 0; }
    .id-card { box-shadow: none !important; page-break-after: always; }
    .id-card:last-of-type { page-break-after: auto; }
  }
</style>
</head>
<body>

  <!-- ======= FRONT ======= -->
  <div class="id-card">
    <div class="seva-band"></div>
    <div class="id-head d-flex align-items-center justify-content-center gap-2">
      <?php if (setting('org_logo')): ?><img src="<?= e(upload_url(setting('org_logo'))) ?>" class="id-logo" alt=""><?php endif; ?>
      <div>
        <div class="id-org-name"><?= e(setting('site_name')) ?></div>
        <div class="id-subtitle">Member ID Card</div>
      </div>
    </div>
    <div class="id-body text-center">
      <?php if (!empty($member['photo'])): ?>
        <img src="<?= e(upload_url($member['photo'])) ?>" class="person-photo" alt="<?= e($member['name']) ?>">
      <?php else: ?>
        <div class="person-photo"><i class="fa-solid fa-user"></i></div>
      <?php endif; ?>
      <table class="id-fields mx-auto" style="max-width: 260px;">
        <tr><th>Member Name</th><td><?= e($member['name']) ?></td></tr>
        <tr><th>Member ID</th><td><?= e($member['member_no']) ?></td></tr>
        <tr><th>Membership Type</th><td><?= e($category['name'] ?? 'General Member') ?></td></tr>
        <tr><th>Mobile No</th><td><?= e($member['phone']) ?></td></tr>
        <tr><th>Valid Till</th><td><?= $member['valid_till'] ? format_date($member['valid_till']) : 'Lifetime/Renewal pending' ?></td></tr>
      </table>
    </div>
    <div class="id-sign">
      <?php if (setting('cert_signature_image')): ?><img src="<?= e(upload_url(setting('cert_signature_image'))) ?>" style="height:26px;" alt=""><?php endif; ?>
      <div class="line">Authorized Signature</div>
      <div class="org"><?= e(setting('site_name')) ?></div>
    </div>
    <div class="seva-band"></div>
  </div>

  <!-- ======= BACK ======= -->
  <div class="id-card">
    <div class="seva-band"></div>
    <div class="id-head d-flex align-items-center justify-content-center">
      <div class="id-org-name"><?= e(setting('site_name')) ?></div>
    </div>
    <div class="id-body back-mission">
      <h6>Our Mission</h6>
      <p><?= e($mission['content'] ?? 'Building an empowered society through education, service and empowerment.') ?></p>

      <?php $benefits = array_filter(array_map('trim', explode("\n", setting('membership_benefits')))); ?>
      <?php if ($benefits): ?>
      <h6>Membership Benefits</h6>
      <ul>
        <?php foreach ($benefits as $b): ?><li><?= e($b) ?></li><?php endforeach; ?>
      </ul>
      <?php endif; ?>

      <h6>Important Notice</h6>
      <p>This ID Card certifies the official membership of the holder with <?= e(setting('site_name')) ?>. This ID Card becomes invalid after the expiry of the membership period.</p>

      <div class="back-contact">
        <div><strong>Website:</strong> <?= e(setting('org_website') ?: BASE_URL) ?></div>
        <?php if (setting('site_email')): ?><div><strong>Email:</strong> <?= e(setting('site_email')) ?></div><?php endif; ?>
        <?php if (setting('site_phone')): ?><div><strong>Contact:</strong> <?= e(setting('site_phone')) ?></div><?php endif; ?>
        <?php if (setting('site_address')): ?><div><strong>Address:</strong> <?= e(setting('site_address')) ?></div><?php endif; ?>
      </div>

      <p class="back-notice">Note: This ID Card remains the property of <?= e(setting('site_name')) ?>. In case of loss, the Foundation should be informed immediately.</p>
    </div>
    <div class="id-sign">
      <?php if (setting('cert_signature_image')): ?><img src="<?= e(upload_url(setting('cert_signature_image'))) ?>" style="height:26px;" alt=""><?php endif; ?>
      <div class="line">Authorized Signature</div>
      <div class="org"><?= e(setting('site_name')) ?></div>
    </div>
    <div class="seva-band"></div>
  </div>

  <div class="actions text-center no-print">
    <button class="btn btn-blue w-100 mt-3" onclick="window.print()">Print / Save as PDF</button>
    <a href="<?= url('membership/dashboard') ?>" class="btn btn-outline-nav w-100 mt-2">Back to dashboard</a>
  </div>
</body>
</html>
