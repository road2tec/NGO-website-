<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if ($action === 'update_status' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $status = post('status');
    $valid = ['new','under_review','shortlisted','interview','selected','rejected','withdrawn'];
    if (in_array($status, $valid, true)) {
        Database::update('job_applications', ['status' => $status, 'admin_notes' => post('admin_notes')], 'id=?', [$id]);
        flash_set('success', 'Application updated.');
    }
    redirect('admin/index.php?page=job_applications' . (get_param('job_id') ? '&job_id=' . (int) get_param('job_id') : ''));
}

$jobFilter    = (int) get_param('job_id');
$statusFilter = get_param('status', 'all');
$conditions = [];
$params = [];
if ($jobFilter) { $conditions[] = 'ja.job_id = ?'; $params[] = $jobFilter; }
if ($statusFilter !== 'all') { $conditions[] = 'ja.status = ?'; $params[] = $statusFilter; }
$where = $conditions ? implode(' AND ', $conditions) : '1=1';

$applications = Database::all(
    "SELECT ja.*, j.title AS job_title FROM job_applications ja
     JOIN jobs j ON j.id = ja.job_id WHERE $where ORDER BY ja.created_at DESC", $params
);
$allJobs = Database::all("SELECT id, title FROM jobs ORDER BY title");
$statusLabels = ['new'=>'New','under_review'=>'Under Review','shortlisted'=>'Shortlisted','interview'=>'Interview','selected'=>'Selected','rejected'=>'Rejected','withdrawn'=>'Withdrawn'];
$statusBadge = ['new'=>'badge-orange','under_review'=>'badge-orange','shortlisted'=>'badge-green','interview'=>'badge-green','selected'=>'badge-green','rejected'=>'bg-danger-subtle text-danger','withdrawn'=>'bg-secondary-subtle text-secondary'];
?>
<div class="admin-card mb-3">
  <form method="get" class="row g-2 align-items-end">
    <input type="hidden" name="page" value="job_applications">
    <div class="col-md-5">
      <label class="form-label small mb-1">Job</label>
      <select class="form-select" name="job_id" onchange="this.form.submit()">
        <option value="">All jobs</option>
        <?php foreach ($allJobs as $j): ?>
          <option value="<?= (int) $j['id'] ?>" <?= $jobFilter === (int) $j['id'] ? 'selected' : '' ?>><?= e($j['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-5">
      <label class="form-label small mb-1">Status</label>
      <select class="form-select" name="status" onchange="this.form.submit()">
        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All statuses</option>
        <?php foreach ($statusLabels as $val => $label): ?>
          <option value="<?= $val ?>" <?= $statusFilter === $val ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="table table-admin align-middle">
      <thead><tr><th>Applicant</th><th>Job</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($applications as $a): ?>
        <tr>
          <td><?= e($a['full_name']) ?><br><span class="small text-muted"><?= e($a['email']) ?> &middot; <?= e($a['phone']) ?></span></td>
          <td><?= e($a['job_title']) ?></td>
          <td><span class="badge-type <?= $statusBadge[$a['status']] ?? 'badge-orange' ?>"><?= e($statusLabels[$a['status']] ?? $a['status']) ?></span></td>
          <td class="small"><?= format_date($a['created_at']) ?></td>
          <td class="text-end text-nowrap">
            <?php if ($a['resume_file']): ?>
              <a class="btn btn-sm btn-outline-nav" href="<?= admin_url('download.php?type=job_resume&id=' . $a['id']) ?>" target="_blank" rel="noopener noreferrer">Resume</a>
            <?php endif; ?>
            <button class="btn btn-sm btn-blue" type="button" data-bs-toggle="collapse" data-bs-target="#app-<?= $a['id'] ?>">Manage</button>
          </td>
        </tr>
        <tr class="collapse" id="app-<?= $a['id'] ?>">
          <td colspan="5">
            <form method="post" action="<?= admin_url('index.php?page=job_applications&action=update_status&id=' . $a['id'] . '&job_id=' . $jobFilter) ?>" class="row g-2 align-items-end">
              <?= csrf_field() ?>
              <?php if ($a['cover_letter']): ?><div class="col-12 small text-muted mb-1"><strong>Cover letter:</strong> <?= nl2br(e($a['cover_letter'])) ?></div><?php endif; ?>
              <?php if ($a['skills']): ?><div class="col-12 small text-muted mb-1"><strong>Skills:</strong> <?= e($a['skills']) ?></div><?php endif; ?>
              <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select class="form-select form-select-sm" name="status">
                  <?php foreach ($statusLabels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $a['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-7">
                <label class="form-label small mb-1">Internal notes</label>
                <input class="form-control form-control-sm" name="admin_notes" value="<?= e($a['admin_notes'] ?? '') ?>">
              </div>
              <div class="col-md-2"><button class="btn btn-sm btn-green w-100" type="submit">Save</button></div>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$applications): ?><tr><td colspan="5" class="text-center text-muted py-4">No applications in this filter.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
