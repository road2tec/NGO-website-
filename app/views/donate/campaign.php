<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('donate/campaigns') ?>">Crowdfunding</a></li><li class="breadcrumb-item active"><?= e($campaign['title']) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($campaign['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7">
        <?php if (!empty($campaign['image'])): ?>
          <img src="<?= e(upload_url($campaign['image'])) ?>" class="w-100 rounded-ngo mb-4" alt="<?= e($campaign['title']) ?>">
        <?php endif; ?>
        <div class="d-flex justify-content-between mb-2">
          <strong><?= format_inr($campaign['raised_amount']) ?> raised</strong><span class="text-muted">Goal: <?= format_inr($campaign['goal_amount']) ?></span>
        </div>
        <div class="progress-seva mb-4"><div class="progress-bar" style="width: <?= min(100, round($campaign['raised_amount']/max(1,$campaign['goal_amount'])*100)) ?>%"></div></div>
        <p class="text-muted"><?= nl2br(e($campaign['description'])) ?></p>

        <?php if ($donors): ?>
        <h6 class="fw-bold mt-4 mb-3">Recent supporters</h6>
        <ul class="list-unstyled">
          <?php foreach ($donors as $d): ?>
            <li class="d-flex justify-content-between border-bottom py-2 small">
              <span><?= e($d['donor_name']) ?></span><span class="fw-bold text-blue"><?= format_inr($d['amount']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
      <div class="col-lg-5">
        <div class="form-card" data-aos="fade-up">
          <h5 class="fw-bold mb-3">Support this campaign</h5>
          <form method="post" action="<?= url('donate/campaign/' . $campaign['slug']) ?>">
            <?= csrf_field() ?>
            <div class="mb-3"><label class="form-label" for="c-name">Your name</label><input class="form-control" id="c-name" name="donor_name" required></div>
            <div class="mb-3"><label class="form-label" for="c-amount">Amount (₹)</label><input type="number" min="1" class="form-control" id="c-amount" name="amount" required></div>
            <div class="mb-3"><label class="form-label" for="c-email">Email</label><input type="email" class="form-control" id="c-email" name="email"></div>
            <div class="mb-3">
              <label class="form-label" for="c-method">Payment method</label>
              <select class="form-select" id="c-method" name="method">
                <option value="upi">UPI</option><option value="bank">Bank transfer</option><option value="online">Online</option>
              </select>
            </div>
            <div class="mb-3"><label class="form-label" for="c-cap"><?= e($captcha) ?></label><input class="form-control" id="c-cap" name="captcha" required></div>
            <button class="btn btn-donate btn-lg w-100" type="submit">Contribute</button>
          </form>
          <p class="small text-muted mt-3 mb-0">UPI: <?= e(setting('donate_upi')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
