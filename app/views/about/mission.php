<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('about') ?>">About Us</a></li><li class="breadcrumb-item active">Mission &amp; Vision</li></ol></nav>
    <h1 class="mt-2">Mission &amp; Vision</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-6" data-aos="fade-up">
        <div class="card-ngo p-4 p-lg-5 h-100">
          <i class="fa-solid fa-bullseye fa-2x text-blue mb-3"></i>
          <h3 class="fw-bold mb-3"><?= e($mission['title'] ?? 'Our Mission') ?></h3>
          <p class="text-muted mb-0"><?= nl2br(e($mission['content'] ?? '')) ?></p>
        </div>
      </div>
      <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card-ngo p-4 p-lg-5 h-100">
          <i class="fa-solid fa-seedling fa-2x text-green mb-3"></i>
          <h3 class="fw-bold mb-3"><?= e($vision['title'] ?? 'Our Vision') ?></h3>
          <p class="text-muted mb-0"><?= nl2br(e($vision['content'] ?? '')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
