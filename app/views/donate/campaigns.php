<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('donate') ?>">Donate</a></li><li class="breadcrumb-item active">Crowdfunding</li></ol></nav>
    <h1 class="mt-2">Crowdfunding Campaigns</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($active)): ?>
      <p class="text-muted">No active campaigns right now. Please check back soon.</p>
    <?php else: ?>
    <div class="row g-4 mb-5">
      <?php foreach ($active as $c): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo h-100">
          <div class="card-top-band"></div>
          <?php if (!empty($c['image'])): ?>
            <img src="<?= e(upload_url($c['image'])) ?>" class="card-img-top" alt="<?= e($c['title']) ?>">
          <?php else: ?>
            <div class="card-img-placeholder"><i class="fa-solid fa-hand-holding-heart"></i></div>
          <?php endif; ?>
          <div class="p-4">
            <h5 class="fw-bold"><?= e($c['title']) ?></h5>
            <p class="text-muted small"><?= e(excerpt($c['summary'] ?? '', 100)) ?></p>
            <div class="d-flex justify-content-between small text-muted mb-1">
              <span><?= format_inr($c['raised_amount']) ?> raised</span><span>Goal: <?= format_inr($c['goal_amount']) ?></span>
            </div>
            <div class="progress-seva mb-3"><div class="progress-bar" style="width: <?= min(100, round($c['raised_amount']/max(1,$c['goal_amount'])*100)) ?>%"></div></div>
            <a href="<?= url('donate/campaign/' . $c['slug']) ?>" class="btn btn-donate w-100">Support this campaign</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($closed): ?>
    <h5 class="fw-bold mb-3">Successfully closed campaigns</h5>
    <div class="row g-4">
      <?php foreach ($closed as $c): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type badge-green mb-2 d-inline-block">Completed</span>
          <h6 class="fw-bold"><?= e($c['title']) ?></h6>
          <p class="small text-muted mb-0"><?= format_inr($c['raised_amount']) ?> raised of <?= format_inr($c['goal_amount']) ?> goal</p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
