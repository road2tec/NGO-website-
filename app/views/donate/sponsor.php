<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('donate') ?>">Donate</a></li><li class="breadcrumb-item active">Sponsorship</li></ol></nav>
    <h1 class="mt-2">Sponsorship Programs</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100 text-center">
          <i class="fa-solid fa-child-reaching fa-2x text-blue mb-3"></i>
          <h5 class="fw-bold">Sponsor a Child</h5>
          <p class="small text-muted">₹6,000/year covers a full academic year: fees, books, uniform and mentoring.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="80">
        <div class="card-ngo p-4 h-100 text-center">
          <i class="fa-solid fa-school fa-2x text-orange mb-3"></i>
          <h5 class="fw-bold">Adopt a School</h5>
          <p class="small text-muted">Fund a library, e-learning kit and teacher training for one school for a full year.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="160">
        <div class="card-ngo p-4 h-100 text-center">
          <i class="fa-solid fa-handshake fa-2x text-green mb-3"></i>
          <h5 class="fw-bold">CSR Partnership</h5>
          <p class="small text-muted">Structured, audited CSR partnerships aligned to Schedule VII activities.</p>
        </div>
      </div>
    </div>

    <h4 class="fw-bold mb-4">Our current partners</h4>
    <div class="row g-4">
      <?php foreach ($sponsors as $s): ?>
      <div class="col-6 col-md-3">
        <div class="p-3 rounded-ngo bg-mist h-100 d-flex flex-column align-items-center justify-content-center">
          <i class="fa-solid <?= $s['type']==='government' ? 'fa-landmark' : 'fa-building' ?> fa-2x text-blue mb-2"></i>
          <div class="fw-bold small text-center"><?= e($s['name']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <a href="<?= url('contact') ?>" class="btn btn-donate btn-lg">Discuss a partnership</a>
    </div>
  </div>
</section>
