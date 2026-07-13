<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Volunteer</li></ol></nav>
    <h1 class="mt-2">Volunteer With Us</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="form-card" data-aos="fade-up">
          <form method="post" action="<?= url('volunteer') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="v-name">Name</label><input class="form-control" id="v-name" name="name" required></div>
              <div class="col-md-6"><label class="form-label" for="v-email">Email</label><input type="email" class="form-control" id="v-email" name="email" required></div>
              <div class="col-md-6"><label class="form-label" for="v-phone">Phone</label><input class="form-control" id="v-phone" name="phone" required></div>
              <div class="col-md-6"><label class="form-label" for="v-city">City</label><input class="form-control" id="v-city" name="city"></div>
              <div class="col-md-6"><label class="form-label" for="v-avail">Availability</label><input class="form-control" id="v-avail" name="availability" placeholder="e.g. Weekends, evenings"></div>
              <div class="col-md-6"><label class="form-label" for="v-resume">Resume (optional)</label><input type="file" class="form-control" id="v-resume" name="resume" accept=".pdf,.doc,.docx"></div>
              <div class="col-12"><label class="form-label" for="v-exp">Relevant experience</label><textarea class="form-control" id="v-exp" name="experience" rows="4"></textarea></div>
              <div class="col-md-6"><label class="form-label" for="v-cap"><?= e($captcha) ?></label><input class="form-control" id="v-cap" name="captcha" required></div>
              <div class="col-12"><button class="btn btn-green btn-lg" type="submit">Submit application</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
