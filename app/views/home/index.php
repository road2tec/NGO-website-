<?php $metaDesc = setting('seo_description'); ?>

<!-- ================= HERO ================= -->
<section class="hero">
  <div class="container">
    <div class="swiper hero-swiper">
      <div class="swiper-wrapper">
        <?php foreach ($banners as $i => $b): ?>
        <div class="swiper-slide">
          <div class="row align-items-center g-4">
            <div class="col-lg-6" data-aos="fade-up">
              <div class="eyebrow mb-3">Seva Sankalp Foundation</div>
              <h1 class="mb-3"><?= e($b['title']) ?></h1>
              <p class="lead mb-4"><?= e($b['subtitle']) ?></p>
              <div class="d-flex flex-wrap gap-3">
                <?php if ($b['button_link']): ?>
                  <a href="<?= url($b['button_link']) ?>" class="btn btn-donate btn-lg"><?= e($b['button_text']) ?></a>
                <?php endif; ?>
                <a href="<?= url('about') ?>" class="btn btn-outline-nav btn-lg">Our story</a>
              </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="120">
              <div class="hero-visual">
                <?php if (!empty($b['image'])): ?>
                  <img src="<?= e(upload_url($b['image'])) ?>" alt="<?= e($b['title']) ?>">
                <?php else: ?>
                  <i class="fa-solid fa-hands-holding-child placeholder-icon" aria-hidden="true"></i>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination mt-4"></div>
    </div>
  </div>
</section>

<!-- ================= STATS ================= -->
<section class="stats-strip py-5">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col-6 col-md-3"><div class="stat-num" data-count="<?= (int) setting('stat_members') ?>">0</div><div class="stat-label">Members</div></div>
      <div class="col-6 col-md-3"><div class="stat-num" data-count="<?= (int) setting('stat_projects') ?>">0</div><div class="stat-label">Active Projects</div></div>
      <div class="col-6 col-md-3"><div class="stat-num" data-count="<?= (int) setting('stat_beneficiaries') ?>">0</div><div class="stat-label">Beneficiaries</div></div>
      <div class="col-6 col-md-3"><div class="stat-num" data-count="<?= (int) setting('stat_villages') ?>">0</div><div class="stat-label">Villages Reached</div></div>
    </div>
  </div>
</section>

<!-- ================= WHO WE ARE ================= -->
<section class="section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="hero-visual">
          <i class="fa-solid fa-people-group placeholder-icon" aria-hidden="true"></i>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="eyebrow mb-2">Who we are</div>
        <h2 class="mb-3"><?= e($about['title'] ?? 'Who We Are') ?></h2>
        <p class="text-muted mb-4"><?= e($about['content'] ?? '') ?></p>
        <div class="row g-3">
          <div class="col-6">
            <div class="p-3 rounded-ngo bg-mist h-100">
              <i class="fa-solid fa-bullseye text-blue mb-2"></i>
              <h6 class="fw-bold">Mission</h6>
              <p class="small text-muted mb-0"><?= e(excerpt($mission['content'] ?? '', 90)) ?></p>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-ngo bg-mist h-100">
              <i class="fa-solid fa-seedling text-green mb-2"></i>
              <h6 class="fw-bold">Vision</h6>
              <p class="small text-muted mb-0"><?= e(excerpt($vision['content'] ?? '', 90)) ?></p>
            </div>
          </div>
        </div>
        <a href="<?= url('about') ?>" class="btn btn-blue mt-4">Read our full story</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= ACHIEVEMENTS ================= -->
<section class="section tint">
  <div class="container">
    <div class="section-head mx-auto text-center mb-5" data-aos="fade-up">
      <div class="eyebrow justify-content-center mb-2">Recognition</div>
      <h2>Achievements &amp; Milestones</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($achievements as $a): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type badge-orange mb-3 d-inline-block"><?= e($a['year']) ?></span>
          <h5 class="fw-bold"><?= e($a['title']) ?></h5>
          <p class="text-muted small mb-0"><?= e($a['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= PROJECTS ================= -->
<section class="section">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
      <div class="section-head">
        <div class="eyebrow mb-2">Where the work happens</div>
        <h2>Featured Projects</h2>
      </div>
      <a href="<?= url('projects') ?>" class="btn btn-outline-nav">View all projects</a>
    </div>
    <div class="row g-4">
      <?php foreach ($projects as $p): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo">
          <div class="card-top-band"></div>
          <?php if (!empty($p['image'])): ?>
            <img src="<?= e(upload_url($p['image'])) ?>" class="card-img-top" alt="<?= e($p['title']) ?>">
          <?php else: ?>
            <div class="card-img-placeholder"><i class="fa-solid fa-diagram-project"></i></div>
          <?php endif; ?>
          <div class="p-4">
            <span class="badge-type badge-blue mb-2 d-inline-block"><?= e(ucfirst($p['type'])) ?></span>
            <h5 class="fw-bold"><?= e($p['title']) ?></h5>
            <p class="text-muted small"><?= e(excerpt($p['summary'] ?? '', 110)) ?></p>
            <a href="<?= url('projects/detail/' . $p['slug']) ?>" class="fw-bold">Read more <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= DONATE BANNER ================= -->
<section class="section pt-0">
  <div class="container">
    <div class="donate-banner p-4 p-lg-5" data-aos="zoom-in">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h3 class="mb-2">Support Us: your ₹500 can fund a month of school supplies</h3>
          <p class="mb-0 opacity-75">100% of your donation is tracked to a project. Tax exemption available under 80G.</p>
        </div>
        <div class="col-lg-5 text-lg-end">
          <a href="<?= url('donate') ?>" class="btn btn-donate btn-lg me-2">Donate Now</a>
          <a href="<?= url('donate/campaigns') ?>" class="btn btn-outline-light btn-lg mt-2 mt-lg-0">Crowdfunding</a>
        </div>
      </div>
      <?php if ($campaign): ?>
      <div class="mt-4 pt-4 border-top border-light border-opacity-25">
        <div class="d-flex justify-content-between small mb-2">
          <span><?= e($campaign['title']) ?></span>
          <span><?= format_inr($campaign['raised_amount']) ?> raised of <?= format_inr($campaign['goal_amount']) ?></span>
        </div>
        <div class="progress-seva">
          <div class="progress-bar" style="width: <?= min(100, round($campaign['raised_amount'] / max(1,$campaign['goal_amount']) * 100)) ?>%"></div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ================= ACTIVITIES ================= -->
<section class="section tint">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
      <div class="section-head">
        <div class="eyebrow mb-2">On the ground</div>
        <h2>Recent &amp; Upcoming Activities</h2>
      </div>
      <a href="<?= url('activities') ?>" class="btn btn-outline-nav">View all activities</a>
    </div>
    <div class="row g-4">
      <?php foreach (array_merge($upcoming, $activities) as $ev): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo p-4 h-100">
          <span class="badge-type <?= $ev['event_date'] >= date('Y-m-d') ? 'badge-green' : 'badge-blue' ?> mb-2 d-inline-block">
            <?= $ev['event_date'] >= date('Y-m-d') ? 'Upcoming' : 'Completed' ?>
          </span>
          <h5 class="fw-bold"><?= e($ev['title']) ?></h5>
          <p class="text-muted small mb-2"><i class="fa-regular fa-calendar me-1"></i><?= format_date($ev['event_date']) ?> &middot; <i class="fa-solid fa-location-dot ms-2 me-1"></i><?= e($ev['venue']) ?></p>
          <a href="<?= url('activities/detail/' . $ev['slug']) ?>" class="fw-bold">Details <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= VOLUNTEER CTA ================= -->
<section class="section">
  <div class="container">
    <div class="row align-items-center g-4 rounded-ngo bg-mist p-4 p-lg-5" data-aos="fade-up">
      <div class="col-lg-8">
        <div class="eyebrow mb-2">Give your time</div>
        <h3 class="fw-bold mb-2">Volunteers are the backbone of every camp we run</h3>
        <p class="text-muted mb-0">Doctors, teachers, drivers, photographers, or just an extra pair of hands - there's a role for you.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="<?= url('volunteer') ?>" class="btn btn-green btn-lg">Apply to volunteer</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= GALLERY PREVIEW ================= -->
<section class="section tint">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
      <div class="section-head"><div class="eyebrow mb-2">Moments</div><h2>Gallery</h2></div>
      <a href="<?= url('gallery') ?>" class="btn btn-outline-nav">View full gallery</a>
    </div>
    <div class="row g-3">
      <?php foreach ($galleryItems as $g): ?>
      <div class="col-6 col-md-4 col-lg-2" data-aos="fade-up">
        <a href="#" class="gallery-item" data-full="<?= e(upload_url($g['file'])) ?>" data-caption="<?= e($g['caption']) ?>">
          <img src="<?= e(upload_url($g['file'])) ?>" alt="<?= e($g['caption']) ?>">
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= TESTIMONIALS ================= -->
<section class="section">
  <div class="container">
    <div class="section-head mx-auto text-center mb-5" data-aos="fade-up">
      <div class="eyebrow justify-content-center mb-2">In their words</div>
      <h2>What people tell us</h2>
    </div>
    <div class="swiper testimonial-swiper">
      <div class="swiper-wrapper pb-5">
        <?php foreach ($testimonials as $t): ?>
        <div class="swiper-slide">
          <div class="testimonial-card">
            <div class="quote mb-2"><i class="fa-solid fa-quote-left"></i></div>
            <p class="text-muted"><?= e($t['message']) ?></p>
            <div class="fw-bold mt-3"><?= e($t['name']) ?></div>
            <div class="small text-muted"><?= e($t['role']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination mt-3"></div>
    </div>
  </div>
</section>

<!-- ================= NEWS ================= -->
<section class="section tint">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
      <div class="section-head"><div class="eyebrow mb-2">Latest</div><h2>News &amp; Updates</h2></div>
      <a href="<?= url('news') ?>" class="btn btn-outline-nav">View all news</a>
    </div>
    <div class="row g-4">
      <?php foreach ($news as $n): ?>
      <div class="col-md-4" data-aos="fade-up">
        <div class="card-ngo h-100">
          <div class="card-top-band"></div>
          <?php if (!empty($n['image'])): ?>
            <img src="<?= e(upload_url($n['image'])) ?>" class="card-img-top" alt="<?= e($n['title']) ?>">
          <?php else: ?>
            <div class="card-img-placeholder"><i class="fa-solid fa-newspaper"></i></div>
          <?php endif; ?>
          <div class="p-4">
            <span class="badge-type badge-orange mb-2 d-inline-block"><?= e($n['category']) ?></span>
            <h5 class="fw-bold"><?= e($n['title']) ?></h5>
            <p class="text-muted small"><?= e(excerpt($n['excerpt'] ?? '', 100)) ?></p>
            <a href="<?= url('news/detail/' . $n['slug']) ?>" class="fw-bold">Read <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= GOVT / CSR / SPONSORS ================= -->
<section class="section">
  <div class="container text-center" data-aos="fade-up">
    <div class="eyebrow justify-content-center mb-3">Trusted by</div>
    <h2 class="mb-5">Government Recognition, CSR Partners &amp; Sponsors</h2>
    <div class="row g-4 justify-content-center">
      <?php foreach ($sponsors as $s): ?>
      <div class="col-6 col-md-3">
        <div class="p-3 rounded-ngo bg-mist h-100 d-flex flex-column align-items-center justify-content-center">
          <i class="fa-solid <?= $s['type']==='government' ? 'fa-landmark' : 'fa-building' ?> fa-2x text-blue mb-2"></i>
          <div class="fw-bold small"><?= e($s['name']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= ENQUIRY FORM (before footer) ================= -->
<section class="section tint">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="form-card" data-aos="fade-up">
          <div class="eyebrow mb-2">Quick enquiry</div>
          <h3 class="fw-bold mb-4">Have a question or suggestion?</h3>
          <form method="post" action="<?= url('') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="form" value="enquiry">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="eq-name">Name</label>
                <input class="form-control" id="eq-name" name="name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="eq-email">Email</label>
                <input type="email" class="form-control" id="eq-email" name="email" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="eq-phone">Phone (optional)</label>
                <input class="form-control" id="eq-phone" name="phone">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="eq-cap"><?= e($captcha ?? captcha_question()) ?></label>
                <input class="form-control" id="eq-cap" name="captcha" required>
              </div>
              <div class="col-12">
                <label class="form-label" for="eq-msg">Message</label>
                <textarea class="form-control" id="eq-msg" name="message" rows="4" required></textarea>
              </div>
              <div class="col-12">
                <button class="btn btn-donate btn-lg" type="submit">Send message</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= MAP ================= -->
<section class="section pt-0">
  <div class="container">
    <div class="rounded-ngo overflow-hidden shadow-sm" data-aos="fade-up"><?= setting('map_embed') ?></div>
  </div>
</section>

<!-- Lightbox markup -->
<div class="lightbox" id="lightbox"><button class="close-btn" aria-label="Close">&times;</button><img src="" alt=""></div>
