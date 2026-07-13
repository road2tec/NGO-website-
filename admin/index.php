<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/CrudHelper.php';
$admin = require_admin();

$page = get_param('page', 'dashboard');
$allowed = [
    'dashboard','banners','about','people','achievements','certificates','testimonials','news','sponsors',
    'members','membership_categories','projects','events','gallery','campaigns','donations',
    'documents','volunteers','contacts','newsletter','settings','password',
];
if (!in_array($page, $allowed, true)) $page = 'dashboard';

$titles = [
    'dashboard' => 'Dashboard', 'banners' => 'Homepage Banners', 'about' => 'About Sections',
    'people' => 'Board & Team', 'achievements' => 'Achievements', 'certificates' => 'Organisation Certificates',
    'testimonials' => 'Testimonials', 'news' => 'News', 'sponsors' => 'Sponsors / CSR Partners',
    'members' => 'Members', 'membership_categories' => 'Membership Categories',
    'projects' => 'Projects', 'events' => 'Activities / Events', 'gallery' => 'Gallery',
    'campaigns' => 'Crowdfunding Campaigns', 'donations' => 'Donations',
    'documents' => 'Documents', 'volunteers' => 'Volunteer Applications', 'contacts' => 'Contact Messages',
    'newsletter' => 'Newsletter Subscribers', 'settings' => 'Website Settings', 'password' => 'Change Password',
];
$pageTitle = $titles[$page];

require __DIR__ . '/includes/header.php';
require __DIR__ . '/modules/' . $page . '.php';
require __DIR__ . '/includes/footer.php';
