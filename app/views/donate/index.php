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
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold"><?= e(setting('donate_upi')) ?></span>
                <button type="button" class="btn btn-sm btn-outline-nav copy-btn" data-copy-value="<?= e(setting('donate_upi')) ?>">Copy UPI ID</button>
              </div>
            </div>
          </div>
        </div>
        <div class="card-ngo p-4 mb-4">
          <h6 class="fw-bold mb-3">Bank transfer / NEFT</h6>
          <table class="table table-sm table-borderless mb-0">
            <tr><th class="text-muted small fw-normal">Account Name</th><td><?= e(setting('bank_account_name')) ?></td></tr>
            <tr><th class="text-muted small fw-normal">Bank Name</th><td><?= e(setting('bank_name')) ?></td></tr>
            <tr>
              <th class="text-muted small fw-normal">Account Number</th>
              <td class="d-flex align-items-center gap-2 flex-wrap">
                <span><?= e(setting('bank_account_number')) ?></span>
                <button type="button" class="btn btn-sm btn-outline-nav copy-btn" data-copy-value="<?= e(setting('bank_account_number')) ?>">Copy</button>
              </td>
            </tr>
            <tr>
              <th class="text-muted small fw-normal">IFSC Code</th>
              <td class="d-flex align-items-center gap-2 flex-wrap">
                <span><?= e(setting('bank_ifsc')) ?></span>
                <button type="button" class="btn btn-sm btn-outline-nav copy-btn" data-copy-value="<?= e(setting('bank_ifsc')) ?>">Copy</button>
              </td>
            </tr>
            <?php if (setting('bank_branch')): ?>
              <tr><th class="text-muted small fw-normal">Branch</th><td><?= e(setting('bank_branch')) ?></td></tr>
            <?php endif; ?>
          </table>
        </div>
        <div class="card-ngo p-4 d-flex align-items-center gap-3">
          <i class="fa-solid fa-shield-halved fa-2x text-green" aria-hidden="true"></i>
          <div>
            <div class="fw-bold">100% Safe &amp; Secure</div>
            <div class="small text-muted">Your details are used only to record and verify your donation.</div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="form-card" data-aos="fade-up">
          <h4 class="fw-bold mb-3">Record your donation pledge</h4>
          <p class="small text-muted">After transferring, fill this so we can match your payment and email your 80G receipt.</p>
          <form method="post" action="<?= url('donate') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label" for="d-campaign">Campaign</label>
                <select class="form-select" id="d-campaign" name="campaign_id">
                  <option value="">General Fund (no specific campaign)</option>
                  <?php foreach ($campaigns as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"><?= e($c['title']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Donation amount</label>
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <?php foreach ($amountOptions as $opt): ?>
                    <input type="radio" class="btn-check" name="amount_option_id" id="d-amt-<?= (int) $opt['id'] ?>" value="<?= (int) $opt['id'] ?>" autocomplete="off" required>
                    <label class="btn btn-outline-nav" for="d-amt-<?= (int) $opt['id'] ?>"><?= format_inr($opt['amount']) ?></label>
                  <?php endforeach; ?>
                  <input type="radio" class="btn-check" name="amount_option_id" id="d-amt-custom" value="custom" autocomplete="off">
                  <label class="btn btn-outline-nav" for="d-amt-custom">Custom Amount</label>
                </div>
                <input type="number" min="1" class="form-control d-none" id="d-custom-amount" name="custom_amount" placeholder="Enter custom amount (₹)">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="d-fname">First name</label>
                <input class="form-control" id="d-fname" name="first_name" required>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="d-mname">Middle name</label>
                <input class="form-control" id="d-mname" name="middle_name">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="d-sname">Surname</label>
                <input class="form-control" id="d-sname" name="surname" required>
              </div>
              <div class="col-md-6"><label class="form-label" for="d-email">Email</label><input type="email" class="form-control" id="d-email" name="email" required></div>
              <div class="col-md-6"><label class="form-label" for="d-phone">Phone</label><input class="form-control" id="d-phone" name="phone" required></div>
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
        <?php foreach ($campaigns as $c):
          $goal     = max(1, (float) $c['goal_amount']);
          $raised   = (float) $c['raised_amount'];
          $progress = min(100, round($raised / $goal * 100));
        ?>
        <div class="col-md-4" data-aos="fade-up">
          <div class="card-ngo p-4 h-100">
            <h6 class="fw-bold"><?= e($c['title']) ?></h6>
            <p class="small text-muted"><?= e(excerpt($c['summary'] ?? '', 90)) ?></p>
            <div class="progress-seva mb-2"><div class="progress-bar" style="width: <?= $progress ?>%"></div></div>
            <a href="<?= url('donate/campaign/' . $c['slug']) ?>" class="fw-bold">View campaign <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
