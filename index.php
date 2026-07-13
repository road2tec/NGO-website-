<?php
/**
 * Front controller - routes friendly URLs to controllers.
 * URL pattern:  /controller/action/param   e.g. /projects/detail/tree-plantation
 * Falls back to index.php?url=... when mod_rewrite is unavailable.
 */
require_once __DIR__ . '/config/config.php';

$url   = trim($_GET['url'] ?? '', '/');
$parts = $url === '' ? [] : explode('/', $url);

$routeMap = [
    ''            => ['HomeController', 'index'],
    'home'        => ['HomeController', 'index'],
    'about'       => ['AboutController', 'index'],
    'membership'  => ['MembershipController', 'index'],
    'projects'    => ['ProjectsController', 'index'],
    'activities'  => ['ActivitiesController', 'index'],
    'gallery'     => ['GalleryController', 'index'],
    'donate'      => ['DonateController', 'index'],
    'documents'   => ['DocumentsController', 'index'],
    'certificate' => ['CertificateController', 'index'],
    'contact'     => ['ContactController', 'index'],
    'news'        => ['NewsController', 'index'],
    'volunteer'   => ['VolunteerController', 'index'],
    'page'        => ['PageController', 'index'],
    'newsletter'  => ['HomeController', 'newsletter'],
];

$segment = strtolower($parts[0] ?? '');
if (!isset($routeMap[$segment])) {
    http_response_code(404);
    $pageTitle = 'Page not found';
    require __DIR__ . '/app/views/layouts/header.php';
    require __DIR__ . '/app/views/errors/404.php';
    require __DIR__ . '/app/views/layouts/footer.php';
    exit;
}

[$controllerName, $defaultAction] = $routeMap[$segment];
$action = $parts[1] ?? $defaultAction;
$param  = $parts[2] ?? null;

require_once __DIR__ . '/app/core/Controller.php';
$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';
require_once $controllerFile;

$controller = new $controllerName();
$action = preg_replace('/[^a-z0-9_]/i', '', $action);

if (!method_exists($controller, $action)) {
    // treat second segment as a parameter of the default action, e.g. /news/my-article
    $param  = $parts[1] ?? null;
    $action = $defaultAction;
}

$controller->$action($param);
