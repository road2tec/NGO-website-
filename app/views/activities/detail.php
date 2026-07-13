<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('activities') ?>">Activities</a></li><li class="breadcrumb-item active"><?= e($event['title']) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($event['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7">
        <?php if (!empty($event['image'])): ?>
          <img src="<?= e(upload_url($event['image'])) ?>" class="w-100 rounded-ngo mb-4" alt="<?= e($event['title']) ?>">
        <?php endif; ?>
        <p class="text-muted"><?= nl2br(e($event['description'])) ?></p>
        <div class="row g-3 mt-2">
          <div class="col-6"><div class="p-3 bg-mist rounded-ngo"><small class="text-muted d-block">Date</small><strong><?= format_date($event['event_date']) ?></strong></div></div>
          <div class="col-6"><div class="p-3 bg-mist rounded-ngo"><small class="text-muted d-block">Time</small><strong><?= e($event['event_time']) ?></strong></div></div>
          <div class="col-12"><div class="p-3 bg-mist rounded-ngo"><small class="text-muted d-block">Venue</small><strong><?= e($event['venue']) ?></strong></div></div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="form-card" data-aos="fade-up">
          <div class="eyebrow mb-2">Event ID: <?= e($event['event_code']) ?></div>
          <?php if ($event['event_date'] < date('Y-m-d')): ?>
            <h5 class="fw-bold">This event has already taken place</h5>
            <p class="text-muted small"><?= $regCount ?> people registered. If you attended, you can <a href="<?= url('certificate') ?>">download your participation certificate here</a>.</p>
          <?php elseif (!$event['registration_open']): ?>
            <h5 class="fw-bold">Registration is closed</h5>
          <?php else: ?>
            <h5 class="fw-bold mb-3">Register for this event</h5>
            <form method="post" action="<?= url('activities/detail/' . $event['slug']) ?>">
              <?= csrf_field() ?>
              <div class="mb-3"><label class="form-label" for="ev-name">Name</label><input class="form-control" id="ev-name" name="name" required></div>
              <div class="mb-3"><label class="form-label" for="ev-email">Email</label><input type="email" class="form-control" id="ev-email" name="email" required></div>
              <div class="mb-3"><label class="form-label" for="ev-phone">Phone</label><input class="form-control" id="ev-phone" name="phone" required></div>
              <div class="mb-3"><label class="form-label" for="ev-cap"><?= e(captcha_question()) ?></label><input class="form-control" id="ev-cap" name="captcha" required></div>
              <button class="btn btn-donate w-100" type="submit">Register</button>
              <p class="small text-muted mt-2 mb-0">Save the Event ID above - you'll need it to download your certificate afterwards.</p>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
