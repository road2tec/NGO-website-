<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('careers') ?>">Careers</a></li><li class="breadcrumb-item active"><?= e($job['title']) ?></li></ol></nav>
    <h1 class="mt-2"><?= e($job['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7">
        <div class="d-flex flex-wrap gap-3 mb-4 small text-muted">
          <?php if ($job['category_name']): ?><span><i class="fa-solid fa-layer-group me-1"></i><?= e($job['category_name']) ?><?php if ($job['subcategory_name']): ?> &middot; <?= e($job['subcategory_name']) ?><?php endif; ?></span><?php endif; ?>
          <?php if ($job['location']): ?><span><i class="fa-solid fa-location-dot me-1"></i><?= e($job['location']) ?></span><?php endif; ?>
          <span><i class="fa-solid fa-briefcase me-1"></i><?= e(ucwords(str_replace('_', ' ', $job['employment_type']))) ?></span>
          <?php if ($job['experience']): ?><span><i class="fa-solid fa-chart-line me-1"></i><?= e($job['experience']) ?></span><?php endif; ?>
          <?php if ($job['salary_range']): ?><span><i class="fa-solid fa-sack-dollar me-1"></i><?= e($job['salary_range']) ?></span><?php endif; ?>
        </div>

        <?php if ($job['description']): ?><p class="text-muted"><?= nl2br(e($job['description'])) ?></p><?php endif; ?>

        <?php if ($job['responsibilities']): ?>
        <h6 class="fw-bold mt-4">Responsibilities</h6>
        <p class="text-muted"><?= nl2br(e($job['responsibilities'])) ?></p>
        <?php endif; ?>

        <?php if ($job['required_skills']): ?>
        <h6 class="fw-bold mt-4">Required skills</h6>
        <p class="text-muted"><?= nl2br(e($job['required_skills'])) ?></p>
        <?php endif; ?>

        <?php if ($job['preferred_skills']): ?>
        <h6 class="fw-bold mt-4">Preferred skills</h6>
        <p class="text-muted"><?= nl2br(e($job['preferred_skills'])) ?></p>
        <?php endif; ?>

        <ul class="list-unstyled small text-muted mt-4">
          <?php if ($job['education']): ?><li><strong>Education:</strong> <?= e($job['education']) ?></li><?php endif; ?>
          <li><strong>Openings:</strong> <?= (int) $job['openings'] ?></li>
          <?php if ($job['deadline']): ?><li><strong>Application deadline:</strong> <?= format_date($job['deadline'], 'd F Y') ?></li><?php endif; ?>
        </ul>
      </div>

      <div class="col-lg-5">
        <div class="form-card">
          <h5 class="fw-bold mb-3">Apply for this position</h5>
          <form method="post" action="<?= url('careers/detail/' . $job['slug']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-12"><label class="form-label" for="ja-name">Full name</label><input class="form-control" id="ja-name" name="full_name" required></div>
              <div class="col-md-6"><label class="form-label" for="ja-email">Email</label><input type="email" class="form-control" id="ja-email" name="email" required></div>
              <div class="col-md-6"><label class="form-label" for="ja-phone">Phone</label><input class="form-control" id="ja-phone" name="phone" required></div>
              <div class="col-md-6"><label class="form-label" for="ja-location">Current location</label><input class="form-control" id="ja-location" name="location"></div>
              <div class="col-md-6"><label class="form-label" for="ja-education">Education</label><input class="form-control" id="ja-education" name="education"></div>
              <div class="col-md-6"><label class="form-label" for="ja-experience">Experience</label><input class="form-control" id="ja-experience" name="experience" placeholder="e.g. 2 years"></div>
              <div class="col-md-6"><label class="form-label" for="ja-resume">Resume / CV</label><input type="file" class="form-control" id="ja-resume" name="resume" accept=".pdf,.doc,.docx"></div>
              <div class="col-12"><label class="form-label" for="ja-skills">Key skills</label><input class="form-control" id="ja-skills" name="skills" placeholder="Comma-separated"></div>
              <div class="col-12"><label class="form-label" for="ja-cover">Cover letter (optional)</label><textarea class="form-control" id="ja-cover" name="cover_letter" rows="4"></textarea></div>
              <div class="col-12"><?php $captchaFieldId = 'ja-cap'; require __DIR__ . '/../layouts/captcha_field.php'; ?></div>
              <div class="col-12"><button class="btn btn-blue btn-lg w-100" type="submit">Submit application</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
