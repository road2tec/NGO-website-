<?php
/** @var array $admin */
$currentPage = get_param('page', 'dashboard');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin - <?= e($pageTitle ?? 'Dashboard') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Mulish:wght@400;600;700&display=swap" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="admin-wrap">
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="brand"><i class="fa-solid fa-hands-holding-child"></i><span>Admin Panel</span></div>
    <nav>
      <a href="<?= admin_url('index.php') ?>" class="<?= $currentPage==='dashboard'?'active':'' ?>"><i class="fa-solid fa-gauge"></i>Dashboard</a>

      <div class="nav-group-title">Website Content</div>
      <a href="<?= admin_url('index.php?page=banners') ?>" class="<?= $currentPage==='banners'?'active':'' ?>"><i class="fa-solid fa-panorama"></i>Homepage Banners</a>
      <a href="<?= admin_url('index.php?page=about') ?>" class="<?= $currentPage==='about'?'active':'' ?>"><i class="fa-solid fa-circle-info"></i>About Sections</a>
      <a href="<?= admin_url('index.php?page=people') ?>" class="<?= $currentPage==='people'?'active':'' ?>"><i class="fa-solid fa-users"></i>Board &amp; Team</a>
      <a href="<?= admin_url('index.php?page=achievements') ?>" class="<?= $currentPage==='achievements'?'active':'' ?>"><i class="fa-solid fa-award"></i>Achievements</a>
      <a href="<?= admin_url('index.php?page=certificates') ?>" class="<?= $currentPage==='certificates'?'active':'' ?>"><i class="fa-solid fa-certificate"></i>Org. Certificates</a>
      <a href="<?= admin_url('index.php?page=testimonials') ?>" class="<?= $currentPage==='testimonials'?'active':'' ?>"><i class="fa-solid fa-comment-dots"></i>Testimonials</a>
      <a href="<?= admin_url('index.php?page=news') ?>" class="<?= $currentPage==='news'?'active':'' ?>"><i class="fa-solid fa-newspaper"></i>News</a>
      <a href="<?= admin_url('index.php?page=sponsors') ?>" class="<?= $currentPage==='sponsors'?'active':'' ?>"><i class="fa-solid fa-building"></i>Sponsors / CSR</a>

      <div class="nav-group-title">Membership</div>
      <a href="<?= admin_url('index.php?page=members') ?>" class="<?= $currentPage==='members'?'active':'' ?>"><i class="fa-solid fa-id-card"></i>Members</a>
      <a href="<?= admin_url('index.php?page=membership_categories') ?>" class="<?= $currentPage==='membership_categories'?'active':'' ?>"><i class="fa-solid fa-layer-group"></i>Membership Categories</a>

      <div class="nav-group-title">Programs</div>
      <a href="<?= admin_url('index.php?page=projects') ?>" class="<?= $currentPage==='projects'?'active':'' ?>"><i class="fa-solid fa-diagram-project"></i>Projects</a>
      <a href="<?= admin_url('index.php?page=events') ?>" class="<?= $currentPage==='events'?'active':'' ?>"><i class="fa-solid fa-calendar-days"></i>Activities / Events</a>
      <a href="<?= admin_url('index.php?page=gallery') ?>" class="<?= $currentPage==='gallery'?'active':'' ?>"><i class="fa-solid fa-images"></i>Gallery</a>

      <div class="nav-group-title">Fundraising</div>
      <a href="<?= admin_url('index.php?page=campaigns') ?>" class="<?= $currentPage==='campaigns'?'active':'' ?>"><i class="fa-solid fa-hand-holding-heart"></i>Crowdfunding</a>
      <a href="<?= admin_url('index.php?page=donations') ?>" class="<?= $currentPage==='donations'?'active':'' ?>"><i class="fa-solid fa-sack-dollar"></i>Donations</a>

      <div class="nav-group-title">Documents &amp; People</div>
      <a href="<?= admin_url('index.php?page=documents') ?>" class="<?= $currentPage==='documents'?'active':'' ?>"><i class="fa-solid fa-file-lines"></i>Documents</a>
      <a href="<?= admin_url('index.php?page=volunteers') ?>" class="<?= $currentPage==='volunteers'?'active':'' ?>"><i class="fa-solid fa-hand-fist"></i>Volunteers</a>
      <a href="<?= admin_url('index.php?page=contacts') ?>" class="<?= $currentPage==='contacts'?'active':'' ?>"><i class="fa-solid fa-envelope-open-text"></i>Contact Messages</a>
      <a href="<?= admin_url('index.php?page=newsletter') ?>" class="<?= $currentPage==='newsletter'?'active':'' ?>"><i class="fa-solid fa-paper-plane"></i>Newsletter</a>

      <div class="nav-group-title">System</div>
      <a href="<?= admin_url('index.php?page=settings') ?>" class="<?= $currentPage==='settings'?'active':'' ?>"><i class="fa-solid fa-gears"></i>Website Settings</a>
      <a href="<?= admin_url('index.php?page=password') ?>" class="<?= $currentPage==='password'?'active':'' ?>"><i class="fa-solid fa-key"></i>Change Password</a>
      <a href="<?= admin_url('logout.php') ?>"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
    </nav>
  </aside>

  <div class="admin-main">
    <div class="admin-topbar">
      <button class="btn btn-outline-nav d-md-none" onclick="document.getElementById('adminSidebar').classList.toggle('open')"><i class="fa-solid fa-bars"></i></button>
      <h5 class="fw-bold mb-0"><?= e($pageTitle ?? 'Dashboard') ?></h5>
      <div class="d-flex align-items-center gap-2">
        <a href="<?= url('') ?>" target="_blank" class="btn btn-outline-nav btn-sm"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View site</a>
        <span class="fw-bold small"><?= e($admin['name'] ?? '') ?></span>
      </div>
    </div>
    <div class="admin-content">
      <?= flash_render() ?>
