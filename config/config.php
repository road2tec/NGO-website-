<?php
/**
 * ------------------------------------------------------------------
 *  NGO Website - Main Configuration
 *  Edit the values below after uploading to your hosting.
 * ------------------------------------------------------------------
 */

// ---- Database credentials (from cPanel > MySQL Databases) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'ngo_website');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ---- Site URL (no trailing slash). Example: https://yourngo.org ----
define('BASE_URL', 'http://localhost/ngo-website');

// ---- Environment: 'development' shows errors, 'production' hides them ----
define('APP_ENV', 'development');

// ---- Session / security ----
define('SESSION_NAME', 'ngo_session');
define('SESSION_TIMEOUT', 1800);          // 30 minutes of inactivity
define('CSRF_TOKEN_NAME', 'csrf_token');

// ---- Uploads ----
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,webp,gif');
define('ALLOWED_DOC_TYPES', 'pdf,doc,docx,jpg,jpeg,png');

// ---- Timezone ----
date_default_timezone_set('Asia/Kolkata');

// ---- Error reporting ----
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ---- Secure session bootstrap ----
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

// ---- Session timeout enforcement ----
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// ---- Autoload core, helpers ----
require_once dirname(__DIR__) . '/app/core/Database.php';
require_once dirname(__DIR__) . '/app/helpers/functions.php';
