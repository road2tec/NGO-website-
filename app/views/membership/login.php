<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('membership') ?>">Membership</a></li><li class="breadcrumb-item active">Login</li></ol></nav>
    <h1 class="mt-2">Member Login</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="form-card" data-aos="fade-up">
          <p class="text-muted small mb-3">Log in with the email and password you used while applying for membership to check your status, download your ID card, or view your dashboard.</p>
          <form method="post" action="<?= url('membership/login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label" for="ml-email">Email</label>
              <input type="email" class="form-control" id="ml-email" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="ml-pass">Password</label>
              <input type="password" class="form-control" id="ml-pass" name="password" required>
            </div>
            <button class="btn btn-blue w-100" type="submit">Log in</button>
          </form>
          <p class="small text-center mt-3 mb-0">Not a member yet? <a href="<?= url('membership/apply') ?>">Apply here</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
