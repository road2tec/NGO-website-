<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Event Certificate</li></ol></nav>
    <h1 class="mt-2">Download Event Participation Certificate</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="form-card" data-aos="fade-up">
          <p class="small text-muted mb-3">Enter the details exactly as used during event registration.</p>
          <form method="post" action="<?= url('certificate') ?>">
            <?= csrf_field() ?>
            <div class="mb-3"><label class="form-label" for="cert-name">Name</label><input class="form-control" id="cert-name" name="name" required></div>
            <div class="mb-3"><label class="form-label" for="cert-email">Email</label><input type="email" class="form-control" id="cert-email" name="email" required></div>
            <div class="mb-3"><label class="form-label" for="cert-code">Event ID</label><input class="form-control" id="cert-code" name="event_code" placeholder="e.g. EVT-2026-BD01" required></div>
            <button class="btn btn-donate btn-lg w-100" type="submit">Get my certificate</button>
          </form>
        </div>
        <?php if ($events): ?>
        <div class="mt-4">
          <h6 class="fw-bold">Recent event IDs</h6>
          <ul class="list-unstyled small text-muted">
            <?php foreach ($events as $ev): ?>
              <li class="border-bottom py-2 d-flex justify-content-between"><span><?= e($ev['title']) ?></span><code><?= e($ev['event_code']) ?></code></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
