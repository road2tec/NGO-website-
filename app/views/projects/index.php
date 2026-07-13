<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Projects</li></ol></nav>
    <h1 class="mt-2"><?= e($labels[$type]) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="gallery-filter d-flex flex-wrap gap-2 mb-5">
      <?php foreach ($labels as $key => $label): ?>
        <a href="<?= url('projects/type/' . $key) ?>" class="btn <?= $key === $type ? 'btn-blue' : 'btn-outline-nav' ?> btn-sm"><?= e($label) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($projects)): ?>
      <p class="text-muted text-center py-5">No <?= strtolower(e($labels[$type])) ?> at the moment. Please check back soon.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($projects as $p): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo h-100">
          <div class="card-top-band"></div>
          <?php if (!empty($p['image'])): ?>
            <img src="<?= e(upload_url($p['image'])) ?>" class="card-img-top" alt="<?= e($p['title']) ?>">
          <?php else: ?>
            <div class="card-img-placeholder"><i class="fa-solid fa-diagram-project"></i></div>
          <?php endif; ?>
          <div class="p-4">
            <span class="badge-type badge-blue mb-2 d-inline-block"><?= e($p['location']) ?></span>
            <h5 class="fw-bold"><?= e($p['title']) ?></h5>
            <p class="text-muted small"><?= e(excerpt($p['summary'] ?? '', 110)) ?></p>
            <a href="<?= url('projects/detail/' . $p['slug']) ?>" class="fw-bold">Read more <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
