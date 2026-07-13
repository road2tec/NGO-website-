<?php
/**
 * Global helper functions used across the public site and admin panel.
 */

/* ---------------- Output escaping (XSS protection) ---------------- */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* ---------------- URLs ---------------- */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function upload_url(?string $path, string $fallback = 'images/placeholder.svg'): string
{
    if (!empty($path) && file_exists(UPLOAD_DIR . '/' . $path)) {
        return UPLOAD_URL . '/' . $path;
    }
    return asset($fallback);
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/* ---------------- Settings (cached key/value store) ---------------- */
function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (Database::all("SELECT setting_key, setting_value FROM settings") as $row) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) { /* table missing during install */ }
    }
    return $cache[$key] ?? $default;
}

function save_setting(string $key, string $value): void
{
    Database::query(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?,?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
        [$key, $value]
    );
}

/* ---------------- CSRF protection ---------------- */
function csrf_token(): string
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_field(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}

function csrf_verify(): bool
{
    $sent = $_POST[CSRF_TOKEN_NAME] ?? '';
    return !empty($sent) && hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $sent);
}

/** Call at the top of every POST handler. Stops the request if the token is bad. */
function require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
        http_response_code(419);
        die('Security check failed (invalid CSRF token). Please go back and try again.');
    }
}

/* ---------------- Flash messages ---------------- */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function flash_render(): string
{
    $f = flash_get();
    if (!$f) return '';
    $map = ['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'];
    $cls = $map[$f['type']] ?? 'info';
    return '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">'
         . e($f['message'])
         . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

/* ---------------- Input helpers ---------------- */
function post(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function get_param(string $key, string $default = ''): string
{
    return trim((string) ($_GET[$key] ?? $default));
}

function valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function valid_phone(string $phone): bool
{
    return (bool) preg_match('/^[0-9+\-\s]{8,15}$/', $phone);
}

/* ---------------- Slugs ---------------- */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . time();
}

function unique_slug(string $table, string $text, int $ignoreId = 0): string
{
    $slug = slugify($text);
    $base = $slug;
    $i = 1;
    while (Database::value("SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id != ?", [$slug, $ignoreId]) > 0) {
        $slug = $base . '-' . (++$i);
    }
    return $slug;
}

/* ---------------- File uploads ---------------- */
/**
 * Handle an uploaded file safely.
 * @return string|null stored relative path (e.g. "gallery/abc123.jpg") or null when no file
 * @throws RuntimeException with a user-friendly message on failure
 */
function handle_upload(string $field, string $subdir, string $allowed = ALLOWED_IMAGE_TYPES): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('File is too large. Maximum size is ' . round(MAX_UPLOAD_SIZE / 1048576) . ' MB.');
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedList = array_map('trim', explode(',', strtolower($allowed)));
    if (!in_array($ext, $allowedList, true)) {
        throw new RuntimeException('File type .' . $ext . ' is not allowed. Allowed: ' . $allowed);
    }
    // MIME sanity check
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $okMimes = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    if (!in_array($mime, $okMimes, true)) {
        throw new RuntimeException('File content does not match an allowed type.');
    }
    $dir = UPLOAD_DIR . '/' . trim($subdir, '/');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '-' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Could not save the uploaded file. Check folder permissions.');
    }
    return trim($subdir, '/') . '/' . $name;
}

function delete_upload(?string $relPath): void
{
    if ($relPath && file_exists(UPLOAD_DIR . '/' . $relPath)) {
        @unlink(UPLOAD_DIR . '/' . $relPath);
    }
}

/* ---------------- Simple math captcha ---------------- */
function captcha_question(): string
{
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    return "$a + $b = ?";
}

function captcha_verify(): bool
{
    $sent = (int) post('captcha');
    $ok = isset($_SESSION['captcha_answer']) && $sent === (int) $_SESSION['captcha_answer'];
    unset($_SESSION['captcha_answer']);
    return $ok;
}

/* ---------------- Formatting ---------------- */
function format_date(?string $date, string $format = 'd M Y'): string
{
    if (empty($date) || $date === '0000-00-00') return '';
    return date($format, strtotime($date));
}

function format_inr($amount): string
{
    return '₹' . number_format((float) $amount, 0);
}

function excerpt(string $text, int $length = 140): string
{
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

/* ---------------- Pagination ---------------- */
function paginate(int $total, int $perPage, int $currentPage): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $pages));
    return [
        'total'   => $total,
        'pages'   => $pages,
        'current' => $currentPage,
        'offset'  => ($currentPage - 1) * $perPage,
        'limit'   => $perPage,
    ];
}

function pagination_links(array $p, string $baseUrl): string
{
    if ($p['pages'] <= 1) return '';
    $sep = (strpos($baseUrl, '?') !== false) ? '&' : '?';
    $html = '<nav aria-label="Pages"><ul class="pagination justify-content-center">';
    for ($i = 1; $i <= $p['pages']; $i++) {
        $active = $i === $p['current'] ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="'
               . e($baseUrl . $sep . 'pg=' . $i) . '">' . $i . '</a></li>';
    }
    return $html . '</ul></nav>';
}

/* ---------------- IDs & codes ---------------- */
function generate_member_no(): string
{
    $year = date('Y');
    $seq  = (int) Database::value(
        "SELECT COUNT(*) FROM members WHERE member_no LIKE ?", ["MEM-$year-%"]
    ) + 1;
    return sprintf('MEM-%s-%04d', $year, $seq);
}

function generate_cert_code(): string
{
    return strtoupper(bin2hex(random_bytes(4)));
}

function generate_receipt_no(): string
{
    return 'RCPT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}
