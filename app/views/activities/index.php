<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Activities</li></ol></nav>
    <h1 class="mt-2"><?= e($heading) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
      <h4 class="fw-bold mb-0">Upcoming Events</h4>
      <a href="<?= url('activities/upcoming') ?>" class="btn btn-outline-nav btn-sm">See all upcoming</a>
    </div>
    <?php if (empty($upcoming)): ?>
      <p class="text-muted">No upcoming events scheduled right now.</p>
    <?php else: ?>
    <div class="row g-4 mb-5">
      <?php foreach ($upcoming as $ev): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type badge-green mb-2 d-inline-block"><?= e(ucfirst(str_replace('_',' ',$ev['type']))) ?></span>
          <h5 class="fw-bold"><?= e($ev['title']) ?></h5>
          <p class="text-muted small mb-2"><i class="fa-regular fa-calendar me-1"></i><?= format_date($ev['event_date']) ?></p>
          <a href="<?= url('activities/detail/' . $ev['slug']) ?>" class="fw-bold">Details / Register <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
      <h4 class="fw-bold mb-0">Recent Activities</h4>
      <a href="<?= url('activities/recent') ?>" class="btn btn-outline-nav btn-sm">See all recent</a>
    </div>
    <?php if (empty($recent)): ?>
      <p class="text-muted">No recent activities recorded yet.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($recent as $ev): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type badge-blue mb-2 d-inline-block"><?= e(ucfirst(str_replace('_',' ',$ev['type']))) ?></span>
          <h5 class="fw-bold"><?= e($ev['title']) ?></h5>
          <p class="text-muted small mb-2"><i class="fa-regular fa-calendar me-1"></i><?= format_date($ev['event_date']) ?></p>
          <a href="<?= url('activities/detail/' . $ev['slug']) ?>" class="fw-bold">Details <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
