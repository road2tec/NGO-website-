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
        <div class="form-card text-center" data-aos="fade-up">
          <span class="brand-mark d-inline-flex mb-3"><i class="fa-solid fa-hands-holding-child"></i></span>
          <h5 class="fw-bold mb-1">Welcome back</h5>
          <p class="text-muted small mb-4">Log in with the email and password you used while applying for membership to check your status, download your ID card, and view notifications.</p>
          <form method="post" action="<?= url('membership/login') ?>" class="text-start">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label" for="ml-email">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" class="form-control" id="ml-email" name="email" required autofocus>
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label" for="ml-pass">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="ml-pass" name="password" required>
              </div>
            </div>
            <div class="text-end mb-3">
              <a href="<?= url('membership/forgot') ?>" class="small">Forgot password?</a>
            </div>
            <button class="btn btn-blue w-100 btn-lg" type="submit">Log in</button>
          </form>
          <p class="small text-center mt-3 mb-0">Not a member yet? <a href="<?= url('membership/apply') ?>">Apply here</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
