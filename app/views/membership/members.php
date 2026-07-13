<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('membership') ?>">Membership</a></li><li class="breadcrumb-item active">Members</li></ol></nav>
    <h1 class="mt-2">Our Members</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <form method="get" class="row g-2 mb-4 justify-content-center">
      <div class="col-md-5">
        <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search by name or member number">
      </div>
      <div class="col-auto"><button class="btn btn-blue">Search</button></div>
    </form>

    <?php if (empty($members)): ?>
      <p class="text-muted text-center">No approved members found<?= $q ? ' for "' . e($q) . '"' : '' ?>.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($members as $m): ?>
      <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
        <div class="card-ngo person-card p-3 h-100">
          <?php if (!empty($m['photo'])): ?>
            <img src="<?= e(upload_url($m['photo'])) ?>" class="person-photo" alt="<?= e($m['name']) ?>">
          <?php else: ?>
            <div class="person-photo"><i class="fa-solid fa-user"></i></div>
          <?php endif; ?>
          <h6 class="fw-bold mb-0"><?= e($m['name']) ?></h6>
          <div class="small text-blue fw-bold"><?= e($m['member_no']) ?></div>
          <div class="small text-muted"><?= e($m['occupation']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4"><?= pagination_links($p, url('membership/members') . ($q ? '?q=' . urlencode($q) : '')) ?></div>
    <?php endif; ?>
  </div>
</section>
