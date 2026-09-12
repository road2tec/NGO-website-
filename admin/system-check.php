<?php
/**
 * Admin-only deployment health check. Verifies the vendored libraries and
 * server capabilities the certificate/email/export system depends on are
 * actually present and correct on THIS server - visit this page after every
 * upload instead of guessing from a cryptic crash on some other page.
 */
require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

function check_file(string $path, ?int $expectMinBytes = null): array
{
    $full = dirname(__DIR__) . '/' . ltrim($path, '/');
    if (!file_exists($full)) return ['ok' => false, 'detail' => 'MISSING - file does not exist on this server'];
    if (!is_readable($full)) return ['ok' => false, 'detail' => 'NOT READABLE - exists but permissions block reading it'];
    $size = filesize($full);
    if ($expectMinBytes !== null && $size < $expectMinBytes) {
        return ['ok' => false, 'detail' => "TRUNCATED - only $size bytes (expected at least $expectMinBytes) - re-upload this file"];
    }
    return ['ok' => true, 'detail' => number_format($size) . ' bytes, modified ' . date('d M Y H:i', filemtime($full))];
}

/**
 * A file can exist with the right size yet still be an OLD VERSION if a
 * previous upload only replaced some files - this happened for real: the
 * vendored libraries and functions.php were current, but admin/modules/
 * settings.php was still an older copy. Checking for a string that only
 * exists in the current source is the only way to catch that.
 */
function check_contains(string $path, string $needle, string $reason): array
{
    $full = dirname(__DIR__) . '/' . ltrim($path, '/');
    if (!file_exists($full)) return ['ok' => false, 'detail' => 'MISSING - file does not exist on this server'];
    $contents = file_get_contents($full);
    if (!str_contains($contents, $needle)) {
        return ['ok' => false, 'detail' => "OLD VERSION - missing \"$reason\", modified " . date('d M Y H:i', filemtime($full)) . ' - re-upload this specific file'];
    }
    return ['ok' => true, 'detail' => 'current version, modified ' . date('d M Y H:i', filemtime($full))];
}

$checks = [
    'PHP GD extension (CAPTCHA images)' => ['ok' => extension_loaded('gd'), 'detail' => extension_loaded('gd') ? 'enabled' : 'MISSING - enable gd in hPanel > PHP Configuration'],
    'TFPDF core' => check_file('app/lib/TFPDF/tfpdf.php', 50000),
    'TFPDF TTF parser' => check_file('app/lib/TFPDF/font/unifont/ttfonts.php', 20000),
    'DejaVuSans.ttf (regular)' => check_file('app/lib/TFPDF/font/unifont/DejaVuSans.ttf', 700000),
    'DejaVuSans-Bold.ttf' => check_file('app/lib/TFPDF/font/unifont/DejaVuSans-Bold.ttf', 700000),
    'PHPMailer core' => check_file('app/lib/PHPMailer/PHPMailer.php', 150000),
    'PHPMailer SMTP' => check_file('app/lib/PHPMailer/SMTP.php', 30000),
    'SimpleXLSXGen (Excel export)' => check_file('app/lib/SimpleXLSXGen/SimpleXLSXGen.php', 40000),
    'uploads/ writable (font cache + uploads)' => ['ok' => is_writable(UPLOAD_DIR), 'detail' => is_writable(UPLOAD_DIR) ? 'writable' : 'NOT WRITABLE - chmod 755 the uploads/ folder'],
];

// Functions that only exist if functions.php itself is the current version -
// proves the CODE deploy is current, not just the vendored files.
$expectedFunctions = ['send_mail', 'generate_donation_certificate_pdf', 'generate_donation_receipt_pdf', 'export_csv', 'export_xlsx', 'captcha_stream_image', 'render_template'];
foreach ($expectedFunctions as $fn) {
    $checks["functions.php has $fn()"] = ['ok' => function_exists($fn), 'detail' => function_exists($fn) ? 'present' : 'MISSING - functions.php on this server is an OLDER version, re-upload it'];
}

// Content-marker checks: catches a file that exists with a plausible size
// but is still an OLDER VERSION because only some files got re-uploaded.
$checks['admin/modules/settings.php is current'] = check_contains('admin/modules/settings.php', 'include_certificate', 'sample-certificate test email checkbox');
$checks['admin/modules/donations.php is current'] = check_contains('admin/modules/donations.php', 'compose_certificate', 'certificate compose/preview flow');
$checks['admin/download.php is current'] = check_contains('admin/download.php', 'RuntimeException', 'clean error handling for missing fonts');
$checks['admin/modules/members.php is current'] = check_contains('admin/modules/members.php', 'export.php?type=members', 'Download Member List button');
$checks['app/helpers/functions.php certificate format is current'] = check_contains('app/helpers/functions.php', 'number_to_words_indian', 'exact-format receipt (amount in words)');

$checks['SMTP host configured'] = ['ok' => (bool) setting('smtp_host'), 'detail' => setting('smtp_host') ?: 'not set - Admin > Settings > Email'];

$failCount = count(array_filter($checks, fn($c) => !$c['ok']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>System Check</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{padding:2rem;background:#f4f7fb;} .ok{color:#1c6b3c;} .bad{color:#b02a37;font-weight:700;}</style>
</head>
<body>
<div class="container" style="max-width:800px;">
  <h3>Deployment health check</h3>
  <p class="text-muted">Run this on the live server after every upload. <?= $failCount ? "<strong class='bad'>$failCount problem(s) found below.</strong>" : "<strong class='ok'>All checks passed.</strong>" ?></p>
  <table class="table table-bordered bg-white">
    <?php foreach ($checks as $label => $c): ?>
      <tr>
        <td><?= htmlspecialchars($label) ?></td>
        <td class="<?= $c['ok'] ? 'ok' : 'bad' ?>"><?= $c['ok'] ? '✓ OK' : '✗ FAIL' ?> - <?= htmlspecialchars($c['detail']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <a href="<?= admin_url('index.php') ?>" class="btn btn-primary">Back to admin</a>
</div>
</body>
</html>
