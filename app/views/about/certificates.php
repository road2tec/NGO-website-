<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('about') ?>">About Us</a></li><li class="breadcrumb-item active">Certificates</li></ol></nav>
    <h1 class="mt-2">Certificates &amp; Legal Information</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4 mb-5">
      <?php foreach ($certificates as $c): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100 text-center">
          <i class="fa-solid fa-certificate fa-2x text-orange mb-3"></i>
          <h6 class="fw-bold"><?= e($c['title']) ?></h6>
          <p class="small text-muted"><?= e($c['description']) ?></p>
          <?php if (!empty($c['file'])): ?>
            <a href="<?= e(upload_url($c['file'])) ?>" class="btn btn-outline-nav btn-sm mt-2" target="_blank" rel="noopener">View</a>
          <?php else: ?>
            <span class="badge bg-secondary-subtle text-secondary">Coming soon</span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="card-ngo p-4 p-lg-5" data-aos="fade-up">
          <h4 class="fw-bold mb-3"><?= e($legal['title'] ?? 'Legal Information') ?></h4>
          <p class="text-muted mb-0"><?= nl2br(e($legal['content'] ?? '')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
