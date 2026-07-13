<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">About Us</li></ol></nav>
    <h1 class="mt-2">Who We Are</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="hero-visual"><i class="fa-solid fa-people-roof placeholder-icon"></i></div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="eyebrow mb-2"><?= e(setting('registration_no')) ?></div>
        <h2 class="mb-3"><?= e($section['title'] ?? '') ?></h2>
        <p class="text-muted"><?= nl2br(e($section['content'] ?? '')) ?></p>
        <div class="d-flex gap-3 mt-4">
          <a href="<?= url('about/mission') ?>" class="btn btn-blue">Mission &amp; Vision</a>
          <a href="<?= url('about/board') ?>" class="btn btn-outline-nav">Meet the board</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="container">
    <div class="section-head mx-auto text-center mb-5" data-aos="fade-up">
      <div class="eyebrow justify-content-center mb-2">Since 2012</div>
      <h2><?= e($history['title'] ?? 'Our History') ?></h2>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="card-ngo p-4 p-lg-5" data-aos="fade-up">
          <p class="text-muted mb-0"><?= nl2br(e($history['content'] ?? '')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container text-center" data-aos="fade-up">
    <h3 class="fw-bold mb-4">Learn more about us</h3>
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="<?= url('about/team') ?>" class="btn btn-outline-nav">Our Team</a>
      <a href="<?= url('about/achievements') ?>" class="btn btn-outline-nav">Achievements</a>
      <a href="<?= url('about/certificates') ?>" class="btn btn-outline-nav">Certificates &amp; Legal Info</a>
      <a href="<?= url('documents') ?>" class="btn btn-outline-nav">Documents</a>
    </div>
  </div>
</section>
