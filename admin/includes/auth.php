<?php
/**
 * Admin bootstrap. Every admin page starts with:
 *   require_once __DIR__ . '/includes/auth.php';
 */
require_once dirname(__DIR__, 2) . '/config/config.php';

define('ADMIN_URL', BASE_URL . '/admin');

function admin_url(string $path = ''): string
{
    return ADMIN_URL . '/' . ltrim($path, '/');
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): array
{
    if (!admin_logged_in()) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
    $admin = Database::one("SELECT * FROM admins WHERE id=?", [$_SESSION['admin_id']]);
    if (!$admin) {
        session_unset();
        header('Location: ' . admin_url('login.php'));
        exit;
    }
    return $admin;
}

/** Render an admin page inside the admin layout */
function admin_render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require dirname(__DIR__) . '/admin/includes/header.php';
    require dirname(__DIR__) . '/admin/modules/' . $view . '.php';
    require dirname(__DIR__) . '/admin/includes/footer.php';
}
