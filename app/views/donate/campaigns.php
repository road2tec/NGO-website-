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
          <?php
            $goal      = max(1, (float) $c['goal_amount']);
            $raised    = (float) $c['raised_amount'];
            $remaining = max(0, $goal - $raised);
            $progress  = min(100, round($raised / $goal * 100));
          ?>
          <div class="p-4">
            <h5 class="fw-bold"><?= e($c['title']) ?></h5>
            <p class="text-muted small"><?= e(excerpt($c['summary'] ?? '', 100)) ?></p>
            <div class="d-flex justify-content-between small text-muted mb-1">
              <span><?= format_inr($raised) ?> raised</span><span>Goal: <?= format_inr($goal) ?></span>
            </div>
            <div class="progress-seva mb-2"><div class="progress-bar" style="width: <?= $progress ?>%"></div></div>
            <div class="small text-muted mb-3"><?= $progress ?>% funded &middot; <?= format_inr($remaining) ?> remaining</div>
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
