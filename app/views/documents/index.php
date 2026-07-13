<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Documents</li></ol></nav>
    <h1 class="mt-2">Documents</h1>
    <p class="mb-0 mt-2 opacity-75">Legal registrations, annual reports and audited statements, published for full transparency.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <form method="get" class="row g-2 mb-4">
      <div class="col-md-6"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search documents"></div>
      <div class="col-md-4">
        <select class="form-select" name="cat" onchange="this.form.submit()">
          <option value="">All categories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= e($c) ?>" <?= $cat === $c ? 'selected' : '' ?>><?= e($c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-blue w-100">Search</button></div>
    </form>

    <?php if (empty($documents)): ?>
      <p class="text-muted">No documents found.</p>
    <?php else: ?>
    <div class="row g-3">
      <?php foreach ($documents as $d): ?>
      <div class="col-md-6" data-aos="fade-up">
        <div class="doc-row">
          <div class="doc-icon"><i class="fa-solid fa-file-lines"></i></div>
          <div class="flex-grow-1">
            <div class="fw-bold"><?= e($d['title']) ?></div>
            <div class="small text-muted"><?= e($d['category']) ?> &middot; <?= (int) $d['downloads'] ?> downloads</div>
          </div>
          <a href="<?= url('documents/download/' . $d['id']) ?>" class="btn btn-outline-nav btn-sm">Download</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
