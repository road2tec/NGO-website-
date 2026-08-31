<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('donate/campaigns') ?>">Crowdfunding</a></li><li class="breadcrumb-item active"><?= e($campaign['title']) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($campaign['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5 align-items-start">
      <div class="col-lg-7">
        <?php if (!empty($campaign['image'])): ?>
          <img src="<?= e(upload_url($campaign['image'])) ?>" class="w-100 rounded-ngo mb-4" alt="<?= e($campaign['title']) ?>">
        <?php endif; ?>
        <?php
          $goal      = max(1, (float) $campaign['goal_amount']);
          $raised    = (float) $campaign['raised_amount'];
          $remaining = max(0, $goal - $raised);
          $progress  = min(100, round($raised / $goal * 100));
        ?>
        <div class="d-flex justify-content-between mb-2 flex-wrap gap-1">
          <strong><?= format_inr($raised) ?> raised</strong><span class="text-muted">Goal: <?= format_inr($goal) ?> &middot; <?= format_inr($remaining) ?> remaining</span>
        </div>
        <div class="progress-seva mb-1"><div class="progress-bar" style="width: <?= $progress ?>%"></div></div>
        <div class="small text-muted mb-4"><?= $progress ?>% funded</div>
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
        <div class="form-card">
          <h5 class="fw-bold mb-3">Support this campaign</h5>
          <form method="post" action="<?= url('donate/campaign/' . $campaign['slug']) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-12">
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <?php foreach ($amountOptions as $opt): ?>
                    <input type="radio" class="btn-check" name="amount_option_id" id="c-amt-<?= (int) $opt['id'] ?>" value="<?= (int) $opt['id'] ?>" autocomplete="off" required>
                    <label class="btn btn-sm btn-outline-nav" for="c-amt-<?= (int) $opt['id'] ?>"><?= format_inr($opt['amount']) ?></label>
                  <?php endforeach; ?>
                  <input type="radio" class="btn-check" name="amount_option_id" id="c-amt-custom" value="custom" autocomplete="off">
                  <label class="btn btn-sm btn-outline-nav" for="c-amt-custom">Custom</label>
                </div>
                <input type="number" min="1" class="form-control d-none" id="c-custom-amount" name="custom_amount" placeholder="Enter custom amount (₹)">
              </div>
              <div class="col-6"><label class="form-label" for="c-fname">First name</label><input class="form-control" id="c-fname" name="first_name" required></div>
              <div class="col-6"><label class="form-label" for="c-sname">Surname</label><input class="form-control" id="c-sname" name="surname" required></div>
              <div class="col-12"><label class="form-label" for="c-email">Email</label><input type="email" class="form-control" id="c-email" name="email" required></div>
              <div class="col-12"><label class="form-label" for="c-phone">Phone</label><input class="form-control" id="c-phone" name="phone" required></div>
              <div class="col-12"><label class="form-label" for="c-address">Address (for your donation receipt)</label><input class="form-control" id="c-address" name="address"></div>
              <div class="col-12">
                <label class="form-label" for="c-method">Payment method</label>
                <select class="form-select" id="c-method" name="method" data-toggle-cheque-fields>
                  <option value="upi">UPI</option><option value="bank">Bank transfer</option><option value="cheque">Cheque</option><option value="online">Online</option>
                </select>
              </div>
              <div class="col-6 d-none cheque-fields"><label class="form-label" for="c-cheque-no">Cheque No.</label><input class="form-control" id="c-cheque-no" name="cheque_no"></div>
              <div class="col-6 d-none cheque-fields"><label class="form-label" for="c-cheque-bank">Bank Name</label><input class="form-control" id="c-cheque-bank" name="donor_bank_name"></div>
              <div class="col-12"><?php $captchaFieldId = 'c-cap'; require __DIR__ . '/../layouts/captcha_field.php'; ?></div>
              <div class="col-12"><button class="btn btn-donate btn-lg w-100" type="submit">Contribute</button></div>
            </div>
          </form>
          <p class="small text-muted mt-3 mb-0">UPI: <?= e(setting('donate_upi')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
