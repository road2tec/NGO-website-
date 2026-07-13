<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('about') ?>">About Us</a></li><li class="breadcrumb-item active">Achievements</li></ol></nav>
    <h1 class="mt-2">Achievements &amp; Awards</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($achievements as $a): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type badge-orange mb-3 d-inline-block"><?= e($a['year']) ?></span>
          <h5 class="fw-bold"><?= e($a['title']) ?></h5>
          <p class="text-muted small mb-0"><?= e($a['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
