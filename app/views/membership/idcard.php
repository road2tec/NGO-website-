<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Member ID Card - <?= e($member['name']) ?></title>
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
  body { background:#eef3f9; padding:2rem 1rem; }
  .actions { max-width:340px; margin:1rem auto 0; }
</style>
</head>
<body>
  <div class="id-card">
    <div class="seva-band"></div>
    <div class="id-head">
      <div class="fw-bold"><?= e(setting('site_name')) ?></div>
      <div class="small opacity-75">Membership Identity Card</div>
    </div>
    <div class="p-3 text-center">
      <?php if (!empty($member['photo'])): ?>
        <img src="<?= e(upload_url($member['photo'])) ?>" class="person-photo" alt="<?= e($member['name']) ?>" style="width:90px;height:90px;">
      <?php else: ?>
        <div class="person-photo" style="width:90px;height:90px;"><i class="fa-solid fa-user"></i></div>
      <?php endif; ?>
      <h5 class="fw-bold mb-0 mt-2"><?= e($member['name']) ?></h5>
      <div class="small text-blue fw-bold mb-2"><?= e($member['member_no']) ?></div>
      <table class="table table-sm table-borderless text-start small mb-2">
        <tr><th class="text-muted">Blood Group</th><td><?= e($member['blood_group'] ?: '-') ?></td></tr>
        <tr><th class="text-muted">Phone</th><td><?= e($member['phone']) ?></td></tr>
        <tr><th class="text-muted">Valid Till</th><td><?= $member['valid_till'] ? format_date($member['valid_till']) : 'Lifetime/Renewal pending' ?></td></tr>
      </table>
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($member['member_no'] . '|' . $member['email']) ?>" alt="QR code" width="90" height="90">
    </div>
    <div class="seva-band"></div>
  </div>
  <div class="actions text-center no-print">
    <button class="btn btn-blue w-100 mt-3" onclick="window.print()">Print / Save as PDF</button>
    <a href="<?= url('membership/dashboard') ?>" class="btn btn-outline-nav w-100 mt-2">Back to dashboard</a>
  </div>
</body>
</html>
