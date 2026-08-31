<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/CrudHelper.php';
$admin = require_admin();

$page = get_param('page', 'dashboard');
$allowed = [
    'dashboard','banners','homepage_buttons','about','people','achievements','certificates','testimonials','news','sponsors',
    'members','membership_categories','locations','projects','events','gallery','campaigns','donations',
    'donation_amounts','documents','volunteers','contacts','newsletter','settings','password',
    'jobs','job_categories','job_subcategories','job_applications',
];
if (!in_array($page, $allowed, true)) $page = 'dashboard';

$titles = [
    'dashboard' => 'Dashboard', 'banners' => 'Homepage Banners', 'homepage_buttons' => 'Homepage Buttons', 'about' => 'About Sections',
    'people' => 'Board & Team', 'achievements' => 'Achievements', 'certificates' => 'Organisation Certificates',
    'testimonials' => 'Testimonials', 'news' => 'News', 'sponsors' => 'Sponsors / CSR Partners',
    'members' => 'Members', 'membership_categories' => 'Membership Categories', 'locations' => 'Location Master Data',
    'projects' => 'Projects', 'events' => 'Activities / Events', 'gallery' => 'Gallery',
    'campaigns' => 'Crowdfunding Campaigns', 'donations' => 'Donations', 'donation_amounts' => 'Donation Amount Cards',
    'documents' => 'Documents', 'volunteers' => 'Volunteer Applications', 'contacts' => 'Contact Messages',
    'newsletter' => 'Newsletter Subscribers', 'settings' => 'Website Settings', 'password' => 'Change Password',
    'jobs' => 'Jobs', 'job_categories' => 'Job Categories', 'job_subcategories' => 'Job Subcategories', 'job_applications' => 'Job Applications',
];
$pageTitle = $titles[$page];

// Buffered so a module's POST handling (redirect()/require_csrf(), which
// both need to send headers) still works even though header.php below
// writes HTML before the module's own logic runs. Without this, every
// admin save/approve/delete ends in a "headers already sent" warning and
// a page that never actually redirects.
ob_start();
require __DIR__ . '/includes/header.php';
require __DIR__ . '/modules/' . $page . '.php';
require __DIR__ . '/includes/footer.php';
ob_end_flush();
