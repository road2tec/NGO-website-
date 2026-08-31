<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active"><?= e($pageTitle) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($pageTitle) ?></h1>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <?php if ($section && $section['content']): ?>
          <p class="text-muted"><?= nl2br(e($section['content'])) ?></p>
        <?php else: ?>
          <p class="text-muted">This page has not been set up yet. Add its content from Admin &rarr; About Sections.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
