<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">News</li></ol></nav>
    <h1 class="mt-2">News &amp; Updates</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($featured): ?>
    <div class="card-ngo mb-5" data-aos="fade-up">
      <div class="row g-0">
        <div class="col-md-5">
          <?php if (!empty($featured['image'])): ?>
            <img src="<?= e(upload_url($featured['image'])) ?>" class="w-100 h-100" style="object-fit:cover" alt="<?= e($featured['title']) ?>">
          <?php else: ?>
            <div class="card-img-placeholder h-100"><i class="fa-solid fa-newspaper"></i></div>
          <?php endif; ?>
        </div>
        <div class="col-md-7 p-4 d-flex flex-column justify-content-center">
          <span class="badge-type badge-orange mb-2 d-inline-block"><?= e($featured['category']) ?></span>
          <h3 class="fw-bold"><?= e($featured['title']) ?></h3>
          <p class="text-muted"><?= e(excerpt($featured['excerpt'] ?? '', 160)) ?></p>
          <a href="<?= url('news/detail/' . $featured['slug']) ?>" class="fw-bold">Read full story <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <form method="get" class="row g-2 mb-4">
      <div class="col-md-6"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search news"></div>
      <div class="col-auto"><button class="btn btn-blue">Search</button></div>
    </form>

    <?php if (empty($items)): ?>
      <p class="text-muted">No articles found.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($items as $n): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo h-100">
          <?php if (!empty($n['image'])): ?>
            <img src="<?= e(upload_url($n['image'])) ?>" class="card-img-top" alt="<?= e($n['title']) ?>">
          <?php else: ?>
            <div class="card-img-placeholder"><i class="fa-solid fa-newspaper"></i></div>
          <?php endif; ?>
          <div class="p-4">
            <span class="badge-type badge-blue mb-2 d-inline-block"><?= e($n['category']) ?></span>
            <h6 class="fw-bold"><?= e($n['title']) ?></h6>
            <p class="text-muted small"><?= e(excerpt($n['excerpt'] ?? '', 90)) ?></p>
            <a href="<?= url('news/detail/' . $n['slug']) ?>" class="fw-bold">Read <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4"><?= pagination_links($p, url('news') . ($q ? '?q=' . urlencode($q) : '')) ?></div>
    <?php endif; ?>
  </div>
</section>
