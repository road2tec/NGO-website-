<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/xml; charset=utf-8');

$staticPages = [
    '', 'about', 'about/mission', 'about/board', 'about/team', 'about/achievements', 'about/certificates',
    'membership', 'membership/apply', 'membership/members',
    'projects', 'projects/type/ongoing', 'projects/type/completed', 'projects/type/upcoming', 'projects/type/government', 'projects/type/csr',
    'activities', 'activities/recent', 'activities/upcoming',
    'gallery', 'donate', 'donate/campaigns', 'donate/sponsor',
    'documents', 'certificate', 'contact', 'news', 'volunteer',
    'page/privacy', 'page/terms', 'page/refund', 'page/disclaimer',
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($staticPages as $p) {
    echo '  <url><loc>' . htmlspecialchars(url($p)) . '</loc><changefreq>weekly</changefreq></url>' . "\n";
}

foreach (Database::all("SELECT slug FROM projects") as $row) {
    echo '  <url><loc>' . htmlspecialchars(url('projects/detail/' . $row['slug'])) . '</loc></url>' . "\n";
}
foreach (Database::all("SELECT slug FROM events") as $row) {
    echo '  <url><loc>' . htmlspecialchars(url('activities/detail/' . $row['slug'])) . '</loc></url>' . "\n";
}
foreach (Database::all("SELECT slug FROM news WHERE is_published=1") as $row) {
    echo '  <url><loc>' . htmlspecialchars(url('news/detail/' . $row['slug'])) . '</loc></url>' . "\n";
}
foreach (Database::all("SELECT slug FROM campaigns WHERE is_active=1") as $row) {
    echo '  <url><loc>' . htmlspecialchars(url('donate/campaign/' . $row['slug'])) . '</loc></url>' . "\n";
}

echo '</urlset>';
