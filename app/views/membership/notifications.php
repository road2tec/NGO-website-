<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('membership/dashboard') ?>">Dashboard</a></li><li class="breadcrumb-item active">Notifications</li></ol></nav>
    <h1 class="mt-2">Notifications</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php if (empty($notifications)): ?>
          <div class="card-ngo p-5 text-center text-muted">
            <i class="fa-regular fa-bell-slash fa-2x mb-3"></i>
            <p class="mb-0">No notifications yet.</p>
          </div>
        <?php else: ?>
          <?php foreach ($notifications as $n): ?>
            <div class="card-ngo p-3 mb-3 <?= !$n['is_read'] ? 'border-start border-4 border-primary' : '' ?>" data-aos="fade-up">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <h6 class="fw-bold mb-1"><?= e($n['title']) ?></h6>
                <span class="small text-muted text-nowrap"><?= format_date($n['created_at'], 'd M Y, h:i A') ?></span>
              </div>
              <p class="mb-0 text-muted"><?= nl2br(e($n['message'])) ?></p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <a href="<?= url('membership/dashboard') ?>" class="btn btn-outline-nav mt-2">Back to dashboard</a>
      </div>
    </div>
  </div>
</section>
