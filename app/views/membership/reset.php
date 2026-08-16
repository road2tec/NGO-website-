<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Reset Password</li></ol></nav>
    <h1 class="mt-2">Set a New Password</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="form-card" data-aos="fade-up">
          <form method="post" action="<?= url('membership/reset/' . $token) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label" for="rp-pass">New password</label>
              <input type="password" class="form-control" id="rp-pass" name="password" minlength="6" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label" for="rp-pass2">Confirm new password</label>
              <input type="password" class="form-control" id="rp-pass2" name="password2" minlength="6" required>
            </div>
            <button class="btn btn-blue w-100" type="submit">Reset password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
