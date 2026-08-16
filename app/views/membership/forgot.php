<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('membership/login') ?>">Login</a></li><li class="breadcrumb-item active">Forgot Password</li></ol></nav>
    <h1 class="mt-2">Forgot Password</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="form-card text-center" data-aos="fade-up">
          <span class="brand-mark d-inline-flex mb-3"><i class="fa-solid fa-key"></i></span>
          <p class="text-muted small mb-4">Enter the email you used while applying for membership. We'll send you a link to set a new password.</p>
          <form method="post" action="<?= url('membership/forgot') ?>" class="text-start">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label" for="fp-email">Email</label>
              <input type="email" class="form-control" id="fp-email" name="email" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label" for="fp-cap"><?= e($captcha) ?></label>
              <input class="form-control" id="fp-cap" name="captcha" required>
            </div>
            <button class="btn btn-blue w-100" type="submit">Send reset link</button>
          </form>
          <p class="small text-center mt-3 mb-0"><a href="<?= url('membership/login') ?>">Back to login</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
