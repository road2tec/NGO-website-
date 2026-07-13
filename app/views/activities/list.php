<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('activities') ?>">Activities</a></li><li class="breadcrumb-item active"><?= e($heading) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($heading) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($events)): ?>
      <p class="text-muted text-center py-5">Nothing to show here yet.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($events as $ev): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type badge-blue mb-2 d-inline-block"><?= e(ucfirst(str_replace('_',' ',$ev['type']))) ?></span>
          <h5 class="fw-bold"><?= e($ev['title']) ?></h5>
          <p class="text-muted small mb-2"><i class="fa-regular fa-calendar me-1"></i><?= format_date($ev['event_date']) ?> &middot; <i class="fa-solid fa-location-dot ms-2 me-1"></i><?= e($ev['venue']) ?></p>
          <a href="<?= url('activities/detail/' . $ev['slug']) ?>" class="fw-bold">Details <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
