<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('news') ?>">News</a></li><li class="breadcrumb-item active"><?= e($item['title']) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($item['title']) ?></h1>
    <p class="mb-0 mt-2 opacity-75"><?= e($item['category']) ?> &middot; <?= format_date($item['published_at'], 'd F Y') ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if (!empty($item['image'])): ?>
          <img src="<?= e(upload_url($item['image'])) ?>" class="w-100 rounded-ngo mb-4" alt="<?= e($item['title']) ?>">
        <?php endif; ?>
        <p class="text-muted"><?= nl2br(e($item['content'])) ?></p>
      </div>
      <div class="col-lg-4">
        <h6 class="fw-bold mb-3">More updates</h6>
        <ul class="list-unstyled">
          <?php foreach ($more as $m): ?>
            <li class="border-bottom py-2"><a href="<?= url('news/detail/' . $m['slug']) ?>"><?= e($m['title']) ?></a><div class="small text-muted"><?= format_date($m['published_at']) ?></div></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
