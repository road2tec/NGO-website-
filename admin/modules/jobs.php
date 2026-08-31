<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'save') {
    require_csrf();
    $data = [
        'title'            => post('title'),
        'category_id'      => (int) post('category_id') ?: null,
        'subcategory_id'   => (int) post('subcategory_id') ?: null,
        'location'         => post('location'),
        'employment_type'  => in_array(post('employment_type'), ['full_time','part_time','contract','internship','volunteer'], true) ? post('employment_type') : 'full_time',
        'experience'       => post('experience'),
        'education'        => post('education'),
        'salary_range'     => post('salary_range'),
        'openings'         => max(1, (int) post('openings')),
        'description'      => post('description'),
        'responsibilities' => post('responsibilities'),
        'required_skills'  => post('required_skills'),
        'preferred_skills' => post('preferred_skills'),
        'deadline'         => post('deadline') ?: null,
        'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
        'is_active'        => isset($_POST['is_active']) ? 1 : 0,
    ];
    if (post('title') === '') {
        flash_set('error', 'Job title is required.');
    } else {
        $data['slug'] = post('slug') ?: unique_slug('jobs', post('title'), $id);
        if ($id) {
            Database::update('jobs', $data, 'id=?', [$id]);
            flash_set('success', 'Job updated.');
        } else {
            Database::insert('jobs', $data);
            flash_set('success', 'Job added.');
        }
    }
    redirect('admin/index.php?page=jobs');
}

if ($action === 'delete' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::delete('jobs', 'id=?', [$id]);
    flash_set('success', 'Job deleted.');
    redirect('admin/index.php?page=jobs');
}

$editRow = ($action === 'edit' && $id) ? Database::one("SELECT * FROM jobs WHERE id=?", [$id]) : null;
$categories = Database::all("SELECT id, name FROM job_categories ORDER BY sort_order, name");
$subcategories = $editRow && $editRow['category_id'] ? job_subcategories((int) $editRow['category_id']) : [];
$jobs = Database::all(
    "SELECT j.*, c.name AS category_name,
     (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id=j.id) AS application_count
     FROM jobs j LEFT JOIN job_categories c ON c.id=j.category_id ORDER BY j.created_at DESC"
);
?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="admin-card">
      <h6 class="fw-bold mb-3"><?= $editRow ? 'Edit job' : 'Add new job' ?></h6>
      <?php if (!$categories): ?>
        <p class="text-muted small">Add at least one <strong>Job Category</strong> first so this job can be filed under it.</p>
      <?php endif; ?>
      <form method="post" data-job-category-group>
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <div class="mb-3"><label class="form-label">Job title</label><input class="form-control" name="title" value="<?= e($editRow['title'] ?? '') ?>" required></div>
        <div class="mb-3"><label class="form-label">URL slug (leave blank to auto-generate)</label><input class="form-control" name="slug" value="<?= e($editRow['slug'] ?? '') ?>"></div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Category</label>
            <select class="form-select" name="category_id" data-job="category">
              <option value="">None</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (int) ($editRow['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Subcategory</label>
            <select class="form-select" name="subcategory_id" data-job="subcategory" data-selected="<?= e($editRow['subcategory_id'] ?? '') ?>" <?= !empty($editRow['category_id']) ? '' : 'disabled' ?>>
              <option value="">None</option>
              <?php foreach ($subcategories as $sc): ?>
                <option value="<?= (int) $sc['id'] ?>" <?= (int) ($editRow['subcategory_id'] ?? 0) === (int) $sc['id'] ? 'selected' : '' ?>><?= e($sc['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Location</label><input class="form-control" name="location" value="<?= e($editRow['location'] ?? '') ?>"></div>
          <div class="col-md-6">
            <label class="form-label">Employment type</label>
            <select class="form-select" name="employment_type">
              <?php foreach (['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship', 'volunteer' => 'Volunteer'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($editRow['employment_type'] ?? 'full_time') === $val ? 'selected' : '' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Experience required</label><input class="form-control" name="experience" value="<?= e($editRow['experience'] ?? '') ?>" placeholder="e.g. 2+ years"></div>
          <div class="col-md-6"><label class="form-label">Education required</label><input class="form-control" name="education" value="<?= e($editRow['education'] ?? '') ?>"></div>
          <div class="col-md-6"><label class="form-label">Salary range (optional)</label><input class="form-control" name="salary_range" value="<?= e($editRow['salary_range'] ?? '') ?>"></div>
          <div class="col-md-6"><label class="form-label">Openings</label><input type="number" min="1" class="form-control" name="openings" value="<?= e((string) ($editRow['openings'] ?? 1)) ?>"></div>
          <div class="col-md-6"><label class="form-label">Application deadline</label><input type="date" class="form-control" name="deadline" value="<?= e($editRow['deadline'] ?? '') ?>"></div>
        </div>
        <div class="mb-3 mt-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"><?= e($editRow['description'] ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Responsibilities</label><textarea class="form-control" name="responsibilities" rows="3"><?= e($editRow['responsibilities'] ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Required skills</label><textarea class="form-control" name="required_skills" rows="2"><?= e($editRow['required_skills'] ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Preferred skills</label><textarea class="form-control" name="preferred_skills" rows="2"><?= e($editRow['preferred_skills'] ?? '') ?></textarea></div>
        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="job-featured" name="is_featured" value="1" <?= !empty($editRow['is_featured']) ? 'checked' : '' ?>><label class="form-check-label" for="job-featured">Featured</label></div>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="job-active" name="is_active" value="1" <?= ($editRow['is_active'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label" for="job-active">Active / published</label></div>
        <button class="btn btn-blue" type="submit"><?= $editRow ? 'Update' : 'Add' ?> job</button>
        <?php if ($editRow): ?><a href="<?= admin_url('index.php?page=jobs') ?>" class="btn btn-outline-nav">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">All jobs (<?= count($jobs) ?>)</h6>
      <div class="table-responsive">
        <table class="table table-admin align-middle">
          <thead><tr><th>Title</th><th>Category</th><th>Applications</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($jobs as $j): ?>
            <tr>
              <td><?= e($j['title']) ?><br><span class="small text-muted"><?= e($j['location']) ?></span></td>
              <td><?= e($j['category_name'] ?? '-') ?></td>
              <td><a href="<?= admin_url('index.php?page=job_applications&job_id=' . $j['id']) ?>"><?= (int) $j['application_count'] ?></a></td>
              <td><span class="badge-type <?= $j['is_active'] ? 'badge-green' : 'bg-secondary-subtle text-secondary' ?>"><?= $j['is_active'] ? 'Active' : 'Inactive' ?></span></td>
              <td class="text-end">
                <a href="<?= admin_url('index.php?page=jobs&action=edit&id=' . $j['id']) ?>" class="btn btn-sm btn-outline-nav"><i class="fa-solid fa-pen"></i></a>
                <form method="post" action="<?= admin_url('index.php?page=jobs&action=delete&id=' . $j['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this job? Its applications will be deleted too.');">
                  <?= csrf_field() ?>
                  <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$jobs): ?><tr><td colspan="5" class="text-center text-muted py-4">No jobs yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
