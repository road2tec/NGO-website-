<?php
/**
 * Authenticated file access for private uploads (currently: member ID
 * proofs) and on-the-fly generated PDFs (donation certificate/receipt
 * previews). Uploaded files never get exposed directly - they live under
 * uploads/private/, which is blocked from direct web access.
 */
require_once __DIR__ . '/includes/auth.php';
require_admin();

$type = get_param('type');
$id   = (int) get_param('id');

if ($type === 'id_proof' && $id) {
    $member = Database::one("SELECT id_proof_file, name FROM members WHERE id=?", [$id]);
    $relPath = $member['id_proof_file'] ?? null;

    if (!$relPath || !file_exists(UPLOAD_DIR . '/' . $relPath)) {
        http_response_code(404);
        die('File not found.');
    }
    $fullPath = UPLOAD_DIR . '/' . $relPath;
    $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($fullPath);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="id-proof-' . $id . '.' . $ext . '"');
    header('Content-Length: ' . filesize($fullPath));
    header('X-Content-Type-Options: nosniff');
    readfile($fullPath);
    exit;
}

if ($type === 'job_resume' && $id) {
    $application = Database::one("SELECT resume_file, full_name FROM job_applications WHERE id=?", [$id]);
    $relPath = $application['resume_file'] ?? null;

    if (!$relPath || !file_exists(UPLOAD_DIR . '/' . $relPath)) {
        http_response_code(404);
        die('File not found.');
    }
    $fullPath = UPLOAD_DIR . '/' . $relPath;
    $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($fullPath);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="resume-' . $id . '.' . $ext . '"');
    header('Content-Length: ' . filesize($fullPath));
    header('X-Content-Type-Options: nosniff');
    readfile($fullPath);
    exit;
}

if (in_array($type, ['donation_certificate', 'donation_receipt'], true) && $id) {
    $donation = Database::one(
        "SELECT d.*, c.title AS campaign_title FROM donations d
         LEFT JOIN campaigns c ON c.id = d.campaign_id WHERE d.id=?", [$id]
    );
    if (!$donation || $donation['status'] !== 'received') {
        http_response_code(404);
        die('Donation not found or not yet marked received.');
    }
    if (empty($donation['cert_code'])) {
        $donation['cert_code'] = generate_cert_code();
        Database::update('donations', ['cert_code' => $donation['cert_code']], 'id=?', [$id]);
    }

    try {
        $pdf = $type === 'donation_certificate'
            ? generate_donation_certificate_pdf($donation)
            : generate_donation_receipt_pdf($donation);
    } catch (RuntimeException $e) {
        http_response_code(500);
        die(e($e->getMessage()));
    }
    $name = ($type === 'donation_certificate' ? 'certificate' : 'receipt') . '-' . $donation['receipt_no'] . '.pdf';
    $disposition = get_param('download') ? 'attachment' : 'inline';

    header('Content-Type: application/pdf');
    header('Content-Disposition: ' . $disposition . '; filename="' . $name . '"');
    header('Content-Length: ' . strlen($pdf));
    header('X-Content-Type-Options: nosniff');
    echo $pdf;
    exit;
}

http_response_code(404);
die('File not found.');
