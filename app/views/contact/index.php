<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Contact Us</li></ol></nav>
    <h1 class="mt-2">Contact Us</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="d-flex gap-3 mb-4">
          <div class="doc-icon"><i class="fa-solid fa-location-dot"></i></div>
          <div><h6 class="fw-bold mb-1">Address</h6><p class="text-muted mb-0 small"><?= e(setting('site_address')) ?></p></div>
        </div>
        <div class="d-flex gap-3 mb-4">
          <div class="doc-icon"><i class="fa-solid fa-phone"></i></div>
          <div><h6 class="fw-bold mb-1">Phone / WhatsApp</h6><p class="text-muted mb-0 small"><?= e(setting('site_phone')) ?></p></div>
        </div>
        <div class="d-flex gap-3 mb-4">
          <div class="doc-icon"><i class="fa-solid fa-envelope"></i></div>
          <div><h6 class="fw-bold mb-1">Email</h6><p class="text-muted mb-0 small"><?= e(setting('site_email')) ?></p></div>
        </div>
        <div class="rounded-ngo overflow-hidden shadow-sm"><?= setting('map_embed') ?></div>
      </div>
      <div class="col-lg-7">
        <div class="form-card" data-aos="fade-up">
          <form method="post" action="<?= url('contact') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="c-name">Name</label><input class="form-control" id="c-name" name="name" required></div>
              <div class="col-md-6"><label class="form-label" for="c-email">Email</label><input type="email" class="form-control" id="c-email" name="email" required></div>
              <div class="col-md-6"><label class="form-label" for="c-phone">Phone</label><input class="form-control" id="c-phone" name="phone"></div>
              <div class="col-md-6"><label class="form-label" for="c-subject">Subject</label><input class="form-control" id="c-subject" name="subject"></div>
              <div class="col-12"><label class="form-label" for="c-msg">Message</label><textarea class="form-control" id="c-msg" name="message" rows="5" required></textarea></div>
              <div class="col-md-6"><label class="form-label" for="c-cap"><?= e($captcha) ?></label><input class="form-control" id="c-cap" name="captcha" required></div>
              <div class="col-12"><button class="btn btn-donate btn-lg" type="submit">Send message</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
