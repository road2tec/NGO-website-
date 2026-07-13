<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('projects') ?>">Projects</a></li><li class="breadcrumb-item active"><?= e($project['title']) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($project['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if (!empty($project['image'])): ?>
          <img src="<?= e(upload_url($project['image'])) ?>" class="w-100 rounded-ngo mb-4" alt="<?= e($project['title']) ?>">
        <?php endif; ?>
        <p class="text-muted"><?= nl2br(e($project['description'])) ?></p>

        <?php if ($images): ?>
        <h5 class="fw-bold mt-4 mb-3">Project gallery</h5>
        <div class="row g-3">
          <?php foreach ($images as $img): ?>
          <div class="col-4"><img src="<?= e(upload_url($img['image'])) ?>" class="rounded-ngo w-100" style="aspect-ratio:4/3;object-fit:cover" alt="<?= e($img['caption']) ?>"></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="col-lg-4">
        <div class="card-ngo p-4" data-aos="fade-up">
          <h6 class="fw-bold mb-3">Project facts</h6>
          <table class="table table-borderless small mb-0">
            <tr><th class="text-muted">Type</th><td><?= e(ucfirst($project['type'])) ?></td></tr>
            <tr><th class="text-muted">Location</th><td><?= e($project['location']) ?></td></tr>
            <tr><th class="text-muted">Started</th><td><?= format_date($project['start_date']) ?></td></tr>
            <?php if ($project['end_date']): ?><tr><th class="text-muted">Completed</th><td><?= format_date($project['end_date']) ?></td></tr><?php endif; ?>
            <?php if ($project['partner']): ?><tr><th class="text-muted">Partner</th><td><?= e($project['partner']) ?></td></tr><?php endif; ?>
          </table>
          <?php if (!empty($project['report_file'])): ?>
            <a href="<?= e(upload_url($project['report_file'])) ?>" class="btn btn-outline-nav w-100 mt-3" target="_blank" rel="noopener">Download project report</a>
          <?php endif; ?>
          <a href="<?= url('donate') ?>" class="btn btn-donate w-100 mt-2">Support this project</a>
        </div>
      </div>
    </div>
  </div>
</section>
