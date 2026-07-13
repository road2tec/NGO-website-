<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Donate</li></ol></nav>
    <h1 class="mt-2">Donate Now</h1>
    <p class="mb-0 mt-2 opacity-75"><?= e(setting('pan_80g')) ?> &middot; Registration No: <?= e(setting('registration_no')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-6">
        <h4 class="fw-bold mb-3">Pay directly</h4>
        <div class="card-ngo p-4 mb-4">
          <div class="row g-3 align-items-center">
            <div class="col-auto">
              <?php if (setting('donate_qr_image')): ?>
                <img src="<?= e(upload_url(setting('donate_qr_image'))) ?>" width="110" alt="Donation QR code">
              <?php else: ?>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=upi://pay?pa=<?= urlencode(setting('donate_upi')) ?>%26pn=<?= urlencode(setting('site_name')) ?>" width="110" alt="UPI QR code">
              <?php endif; ?>
            </div>
            <div class="col">
              <div class="small text-muted">Scan &amp; pay via UPI</div>
              <div class="fw-bold"><?= e(setting('donate_upi')) ?></div>
            </div>
          </div>
        </div>
        <div class="card-ngo p-4">
          <h6 class="fw-bold mb-2">Bank transfer / NEFT</h6>
          <pre class="small text-muted mb-0" style="white-space:pre-wrap;font-family:inherit;"><?= e(setting('donate_bank')) ?></pre>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="form-card" data-aos="fade-up">
          <h4 class="fw-bold mb-3">Record your donation pledge</h4>
          <p class="small text-muted">After transferring, fill this so we can match your payment and email your 80G receipt.</p>
          <form method="post" action="<?= url('donate') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="d-name">Your name</label><input class="form-control" id="d-name" name="donor_name" required></div>
              <div class="col-md-6"><label class="form-label" for="d-amount">Amount (₹)</label><input type="number" min="1" class="form-control" id="d-amount" name="amount" required></div>
              <div class="col-md-6"><label class="form-label" for="d-email">Email</label><input type="email" class="form-control" id="d-email" name="email"></div>
              <div class="col-md-6"><label class="form-label" for="d-phone">Phone</label><input class="form-control" id="d-phone" name="phone"></div>
              <div class="col-md-6">
                <label class="form-label" for="d-method">Payment method</label>
                <select class="form-select" id="d-method" name="method">
                  <option value="upi">UPI</option><option value="bank">Bank transfer</option><option value="cash">Cash</option><option value="online">Online (card/wallet)</option>
                </select>
              </div>
              <div class="col-md-6"><label class="form-label" for="d-txn">Transaction ref. (if available)</label><input class="form-control" id="d-txn" name="txn_ref"></div>
              <div class="col-md-6"><label class="form-label" for="d-pan">PAN (for 80G receipt)</label><input class="form-control" id="d-pan" name="pan"></div>
              <div class="col-md-6"><label class="form-label" for="d-cap"><?= e($captcha) ?></label><input class="form-control" id="d-cap" name="captcha" required></div>
              <div class="col-12"><label class="form-label" for="d-msg">Message (optional)</label><textarea class="form-control" id="d-msg" name="message" rows="2"></textarea></div>
              <div class="col-12"><button class="btn btn-donate btn-lg w-100" type="submit">Submit pledge</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php if ($campaigns): ?>
    <div class="mt-5">
      <h4 class="fw-bold mb-4">Or support a specific campaign</h4>
      <div class="row g-4">
        <?php foreach ($campaigns as $c): ?>
        <div class="col-md-4" data-aos="fade-up">
          <div class="card-ngo p-4 h-100">
            <h6 class="fw-bold"><?= e($c['title']) ?></h6>
            <p class="small text-muted"><?= e(excerpt($c['summary'] ?? '', 90)) ?></p>
            <div class="progress-seva mb-2"><div class="progress-bar" style="width: <?= min(100, round($c['raised_amount']/max(1,$c['goal_amount'])*100)) ?>%"></div></div>
            <a href="<?= url('donate/campaign/' . $c['slug']) ?>" class="fw-bold">View campaign <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
