<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Member Dashboard</li></ol></nav>
    <h1 class="mt-2">Welcome, <?= e($member['name']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card-ngo p-4 text-center" data-aos="fade-up">
          <?php if (!empty($member['photo'])): ?>
            <img src="<?= e(upload_url($member['photo'])) ?>" class="person-photo" alt="<?= e($member['name']) ?>">
          <?php else: ?>
            <div class="person-photo"><i class="fa-solid fa-user"></i></div>
          <?php endif; ?>
          <h5 class="fw-bold mb-0"><?= e($member['name']) ?></h5>
          <div class="small text-muted mb-2"><?= e($member['member_no'] ?: 'Member number pending approval') ?></div>
          <?php
            $statusBadge = ['approved' => 'badge-green', 'pending' => 'badge-orange', 'rejected' => 'bg-danger-subtle text-danger'][$member['status']] ?? 'badge-blue';
          ?>
          <span class="badge-type <?= $statusBadge ?>"><?= e(ucfirst($member['status'])) ?></span>
          <div class="d-grid gap-2 mt-4">
            <?php if ($member['status'] === 'approved'): ?>
              <a href="<?= url('membership/idcard') ?>" class="btn btn-blue">Download ID Card</a>
            <?php endif; ?>
            <a href="<?= url('admin/login.php') ?>" class="btn btn-outline-nav">Login as Admin</a>
            <a href="<?= url('membership/logout') ?>" class="btn btn-outline-nav">Log out</a>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card-ngo p-4" data-aos="fade-up" data-aos-delay="80">
          <h5 class="fw-bold mb-3">Your details</h5>
          <table class="table table-borderless mb-0">
            <tr><th class="text-muted small">Category</th><td><?= e($category['name'] ?? 'Not set') ?></td></tr>
            <tr><th class="text-muted small">Email</th><td><?= e($member['email']) ?></td></tr>
            <tr><th class="text-muted small">Phone</th><td><?= e($member['phone']) ?></td></tr>
            <tr><th class="text-muted small">Occupation</th><td><?= e($member['occupation']) ?></td></tr>
            <tr><th class="text-muted small">Blood group</th><td><?= e($member['blood_group']) ?></td></tr>
            <tr><th class="text-muted small">Applied on</th><td><?= format_date($member['created_at']) ?></td></tr>
            <tr><th class="text-muted small">Valid till</th><td><?= $member['valid_till'] ? format_date($member['valid_till']) : '—' ?></td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
