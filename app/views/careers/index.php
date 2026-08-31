<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Careers</li></ol></nav>
    <h1 class="mt-2">Careers</h1>
    <p class="mb-0 mt-2 opacity-75">Join our team - browse current openings and apply online.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="card-ngo p-3 p-md-4 mb-4">
      <form method="get" action="<?= url('careers') ?>" class="row g-3 align-items-end" data-job-category-group>
        <div class="col-md-3">
          <label class="form-label" for="cf-category">Category</label>
          <select class="form-select" id="cf-category" name="category_id" data-job="category">
            <option value="">All categories</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int) $c['id'] ?>" <?= $categoryId === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="cf-subcategory">Subcategory</label>
          <select class="form-select" id="cf-subcategory" name="subcategory_id" data-job="subcategory" data-selected="<?= (int) $subcategoryId ?>" <?= $categoryId ? '' : 'disabled' ?>>
            <option value="">All subcategories</option>
            <?php foreach ($subcategories as $sc): ?>
              <option value="<?= (int) $sc['id'] ?>" <?= $subcategoryId === (int) $sc['id'] ? 'selected' : '' ?>><?= e($sc['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="cf-location">Location</label>
          <input class="form-control" id="cf-location" name="location" value="<?= e($location) ?>" placeholder="e.g. Pune">
        </div>
        <div class="col-md-2">
          <label class="form-label" for="cf-type">Type</label>
          <select class="form-select" id="cf-type" name="employment_type">
            <option value="">Any</option>
            <?php foreach (['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship', 'volunteer' => 'Volunteer'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= $employmentType === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-1"><button class="btn btn-blue w-100" type="submit">Go</button></div>
      </form>
    </div>

    <?php if ($jobs): ?>
    <div class="row g-4">
      <?php foreach ($jobs as $job): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card-ngo p-4 h-100 d-flex flex-column">
          <?php if ($job['is_featured']): ?><span class="badge-type badge-orange mb-2 align-self-start">Featured</span><?php endif; ?>
          <h6 class="fw-bold"><?= e($job['title']) ?></h6>
          <div class="small text-muted mb-2">
            <?php if ($job['category_name']): ?><?= e($job['category_name']) ?><?php if ($job['subcategory_name']): ?> &middot; <?= e($job['subcategory_name']) ?><?php endif; endif; ?>
          </div>
          <div class="small text-muted mb-2">
            <?php if ($job['location']): ?><i class="fa-solid fa-location-dot me-1"></i><?= e($job['location']) ?><br><?php endif; ?>
            <i class="fa-solid fa-briefcase me-1"></i><?= e(ucwords(str_replace('_', ' ', $job['employment_type']))) ?>
            <?php if ($job['experience']): ?> &middot; <?= e($job['experience']) ?><?php endif; ?>
          </div>
          <p class="small text-muted flex-grow-1"><?= e(excerpt($job['description'] ?? '', 100)) ?></p>
          <?php if ($job['deadline']): ?><div class="small text-muted mb-2">Apply by <?= format_date($job['deadline'], 'd M Y') ?></div><?php endif; ?>
          <a href="<?= url('careers/detail/' . $job['slug']) ?>" class="btn btn-outline-nav mt-auto">View details &amp; apply</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4"><?= pagination_links($pagination, url('careers') . '?category_id=' . (int) $categoryId . '&subcategory_id=' . (int) $subcategoryId . '&location=' . urlencode($location) . '&employment_type=' . urlencode($employmentType)) ?></div>
    <?php else: ?>
    <div class="text-center text-muted py-5">
      <i class="fa-solid fa-briefcase fa-2x mb-3" aria-hidden="true"></i>
      <p class="mb-0">No open positions match these filters right now. Please check back soon.</p>
    </div>
    <?php endif; ?>
  </div>
</section>
