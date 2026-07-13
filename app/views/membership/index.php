<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Membership</li></ol></nav>
    <h1 class="mt-2">Become a Member</h1>
    <p class="mb-0 mt-2 opacity-75 col-lg-7"><?= e(setting('membership_fee_note')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($categories as $c): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100 d-flex flex-column">
          <div class="card-top-band mb-3 rounded"></div>
          <h4 class="fw-bold"><?= e($c['name']) ?></h4>
          <div class="display-font fw-bold text-blue mb-2"><?= format_inr($c['fee']) ?> <span class="fs-6 text-muted fw-normal">/ <?= $c['duration_months'] >= 120 ? 'lifetime' : $c['duration_months'] . ' months' ?></span></div>
          <p class="text-muted small flex-grow-1"><?= e($c['benefits']) ?></p>
          <a href="<?= url('membership/apply') ?>" class="btn btn-blue mt-3">Apply now</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="row mt-5 g-4">
      <div class="col-md-4 text-center" data-aos="fade-up">
        <a href="<?= url('membership/members') ?>" class="card-ngo p-4 d-block text-decoration-none h-100">
          <i class="fa-solid fa-users fa-2x text-blue mb-2"></i>
          <h6 class="fw-bold mb-0">Show Members</h6>
        </a>
      </div>
      <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="80">
        <a href="<?= url('membership/idcard') ?>" class="card-ngo p-4 d-block text-decoration-none h-100">
          <i class="fa-solid fa-id-card fa-2x text-orange mb-2"></i>
          <h6 class="fw-bold mb-0">Download Member ID Card</h6>
        </a>
      </div>
      <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="160">
        <a href="<?= url('membership/status') ?>" class="card-ngo p-4 d-block text-decoration-none h-100">
          <i class="fa-solid fa-clipboard-check fa-2x text-green mb-2"></i>
          <h6 class="fw-bold mb-0">Check Membership Status</h6>
        </a>
      </div>
    </div>
  </div>
</section>
