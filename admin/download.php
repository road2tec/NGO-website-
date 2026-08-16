<?php
/**
 * Authenticated file access for private uploads (currently: member ID
 * proofs). Never expose these paths directly - they live under
 * uploads/private/, which is blocked from direct web access.
 */
require_once __DIR__ . '/includes/auth.php';
require_admin();

$type = get_param('type');
$id   = (int) get_param('id');

if ($type === 'id_proof' && $id) {
    $member = Database::one("SELECT id_proof_file, name FROM members WHERE id=?", [$id]);
    $relPath = $member['id_proof_file'] ?? null;
} else {
    $relPath = null;
}

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
