<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('about') ?>">About Us</a></li><li class="breadcrumb-item active"><?= e($heading) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($heading) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($people)): ?>
      <p class="text-muted text-center">No entries have been added yet.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($people as $p): ?>
      <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
        <div class="card-ngo person-card p-3 h-100">
          <?php if (!empty($p['photo'])): ?>
            <img src="<?= e(upload_url($p['photo'])) ?>" class="person-photo" alt="<?= e($p['name']) ?>">
          <?php else: ?>
            <div class="person-photo"><i class="fa-solid fa-user"></i></div>
          <?php endif; ?>
          <h6 class="fw-bold mb-0"><?= e($p['name']) ?></h6>
          <div class="small text-blue fw-bold mb-2"><?= e($p['designation']) ?></div>
          <p class="small text-muted"><?= e($p['bio']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
