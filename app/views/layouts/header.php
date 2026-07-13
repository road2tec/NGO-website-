<?php
$siteName  = setting('site_name', 'NGO Website');
$pageTitle = isset($pageTitle) ? $pageTitle . ' | ' . $siteName : setting('seo_title', $siteName);
$metaDesc  = $metaDesc ?? setting('seo_description');
$canonical = BASE_URL . '/' . trim($_GET['url'] ?? '', '/');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<meta name="keywords" content="<?= e(setting('seo_keywords')) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($metaDesc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($pageTitle) ?>">
<meta name="twitter:description" content="<?= e($metaDesc) ?>">
<!-- Schema.org -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NGO",
  "name": <?= json_encode($siteName) ?>,
  "url": <?= json_encode(BASE_URL) ?>,
  "email": <?= json_encode(setting('site_email')) ?>,
  "telephone": <?= json_encode(setting('site_phone')) ?>,
  "address": <?= json_encode(setting('site_address')) ?>
}
</script>
<!-- Fonts & libraries -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=Mulish:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- ======= Topbar ======= -->
<div class="topbar">
  <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="topbar-scroll small">
      <i class="fa-solid fa-bullhorn me-2" aria-hidden="true"></i><?= e(setting('announcement')) ?>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div id="google_translate_element" class="translate-slot"></div>
      <div class="topbar-social d-none d-md-flex gap-2">
        <a href="<?= e(setting('facebook_url')) ?>" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="<?= e(setting('instagram_url')) ?>" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="<?= e(setting('twitter_url')) ?>" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="<?= e(setting('youtube_url')) ?>" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
      </div>
    </div>
  </div>
</div>

<!-- ======= Navbar (order: Home | Membership | Projects | Activities | Gallery | Donate | About Us | Contact Us) ======= -->
<nav class="navbar navbar-expand-lg sticky-top site-nav" aria-label="Main navigation">
  <div class="container">
    <a class="navbar-brand" href="<?= url('') ?>">
      <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-hands-holding-child"></i></span>
      <span class="brand-text"><?= e($siteName) ?></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="<?= url('') ?>">Home</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">Membership</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= url('membership/apply') ?>">Apply for Membership</a></li>
            <li><a class="dropdown-item" href="<?= url('membership/benefits') ?>">Benefits &amp; Categories</a></li>
            <li><a class="dropdown-item" href="<?= url('membership/members') ?>">Show Members</a></li>
            <li><a class="dropdown-item" href="<?= url('membership/idcard') ?>">Download Member ID Card</a></li>
            <li><a class="dropdown-item" href="<?= url('membership/status') ?>">Check Membership Status</a></li>
            <li><a class="dropdown-item" href="<?= url('membership/login') ?>">Member Login</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">Projects</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= url('projects/type/ongoing') ?>">Ongoing Projects</a></li>
            <li><a class="dropdown-item" href="<?= url('projects/type/completed') ?>">Completed Projects</a></li>
            <li><a class="dropdown-item" href="<?= url('projects/type/upcoming') ?>">Upcoming Projects</a></li>
            <li><a class="dropdown-item" href="<?= url('projects/type/government') ?>">Government Projects</a></li>
            <li><a class="dropdown-item" href="<?= url('projects/type/csr') ?>">CSR Projects</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">Activities</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= url('activities/recent') ?>">Recent Activities</a></li>
            <li><a class="dropdown-item" href="<?= url('activities/upcoming') ?>">Upcoming Events</a></li>
            <li><a class="dropdown-item" href="<?= url('activities') ?>">All Activities</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?= url('gallery') ?>">Gallery</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">Donate</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= url('donate') ?>">Donate Now</a></li>
            <li><a class="dropdown-item" href="<?= url('donate/campaigns') ?>">Crowdfunding Campaigns</a></li>
            <li><a class="dropdown-item" href="<?= url('donate/sponsor') ?>">Sponsorship Programs</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">About Us</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= url('about') ?>">Who We Are</a></li>
            <li><a class="dropdown-item" href="<?= url('about/mission') ?>">Mission &amp; Vision</a></li>
            <li><a class="dropdown-item" href="<?= url('about/board') ?>">Board Members</a></li>
            <li><a class="dropdown-item" href="<?= url('about/team') ?>">Our Team</a></li>
            <li><a class="dropdown-item" href="<?= url('about/achievements') ?>">Achievements</a></li>
            <li><a class="dropdown-item" href="<?= url('about/certificates') ?>">Certificates</a></li>
            <li><a class="dropdown-item" href="<?= url('documents') ?>">Documents</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="<?= url('contact') ?>">Contact Us</a></li>
      </ul>
      <div class="d-flex gap-2 nav-cta">
        <a href="<?= url('membership/apply') ?>" class="btn btn-outline-nav">Join Us</a>
        <a href="<?= url('donate') ?>" class="btn btn-donate">Donate Now</a>
      </div>
    </div>
  </div>
</nav>

<main id="content">
<div class="container mt-3"><?= flash_render() ?></div>
