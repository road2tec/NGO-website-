<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Verify Certificate</li></ol></nav>
    <h1 class="mt-2">Donation Certificate Verification</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <?php if ($donation): ?>
        <div class="card-ngo p-4">
          <span class="badge-type badge-green mb-3 d-inline-block"><i class="fa-solid fa-circle-check me-1"></i>Valid Certificate</span>
          <table class="table table-borderless mb-0">
            <tr><th class="text-muted small">Donor</th><td><?= e($donation['donor_name']) ?></td></tr>
            <tr><th class="text-muted small">Donated To</th><td><?= e($donation['campaign_title'] ?? 'General Fund') ?></td></tr>
            <tr><th class="text-muted small">Amount</th><td><?= format_inr($donation['amount']) ?></td></tr>
            <tr><th class="text-muted small">Date</th><td><?= format_date($donation['created_at'], 'd F Y') ?></td></tr>
            <tr><th class="text-muted small">Certificate ID</th><td><code><?= e($donation['cert_code']) ?></code></td></tr>
          </table>
        </div>
        <?php else: ?>
        <div class="card-ngo p-4 text-center">
          <span class="badge-type bg-danger-subtle text-danger mb-3 d-inline-block"><i class="fa-solid fa-circle-xmark me-1"></i>Not Found</span>
          <p class="text-muted mb-0">Certificate code "<?= e($code) ?>" could not be verified.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
