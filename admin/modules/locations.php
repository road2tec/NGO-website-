<?php
/** @var array $admin */
if (($admin['role'] ?? '') !== 'superadmin') {
    flash_set('error', 'Only a Super Admin can manage location master data.');
    redirect('admin/index.php');
}

$action = get_param('action', 'list');
// 'state' | 'district' | 'taluka' - the save form posts it as a hidden field,
// the toggle form carries it in its target URL's query string.
$level  = post('level') ?: get_param('level');
$id     = (int) get_param('id');

$tableFor = ['state' => 'states', 'district' => 'districts', 'taluka' => 'talukas'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'save' && isset($tableFor[$level])) {
    require_csrf();
    $table = $tableFor[$level];
    $name  = post('name');
    $code  = post('code') ?: null;
    if ($name === '') {
        flash_set('error', 'Name is required.');
    } else {
        $data = ['name' => $name, 'code' => $code];
        if ($level === 'district') $data['state_id'] = (int) post('state_id');
        if ($level === 'taluka')   $data['district_id'] = (int) post('district_id');
        try {
            if ($id) {
                Database::update($table, $data, 'id=?', [$id]);
                flash_set('success', ucfirst($level) . ' updated.');
            } else {
                $data['sort_order'] = 1 + (int) Database::value("SELECT MAX(sort_order) FROM `$table`");
                Database::insert($table, $data);
                flash_set('success', ucfirst($level) . ' added.');
            }
        } catch (PDOException $e) {
            flash_set('error', 'Could not save: a record with that name may already exist here.');
        }
    }
    redirect('admin/index.php?page=locations'
        . (post('redirect_state') ? '&state_id=' . (int) post('redirect_state') : '')
        . (post('redirect_district') ? '&district_id=' . (int) post('redirect_district') : ''));
}

if ($action === 'toggle' && $id && isset($tableFor[$level]) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $table = $tableFor[$level];
    $current = Database::value("SELECT status FROM `$table` WHERE id=?", [$id]);
    Database::update($table, ['status' => $current === 'active' ? 'inactive' : 'active'], 'id=?', [$id]);
    flash_set('success', ucfirst($level) . ' status updated.');
    redirect('admin/index.php?page=locations'
        . (get_param('state_id') ? '&state_id=' . (int) get_param('state_id') : '')
        . (get_param('district_id') ? '&district_id=' . (int) get_param('district_id') : ''));
}

$stateId    = (int) get_param('state_id');
$districtId = (int) get_param('district_id');

$states = Database::all("SELECT s.*, (SELECT COUNT(*) FROM districts d WHERE d.state_id=s.id) AS district_count
                          FROM states s ORDER BY s.sort_order, s.name");
$districts = $stateId ? Database::all(
    "SELECT d.*, (SELECT COUNT(*) FROM talukas t WHERE t.district_id=d.id) AS taluka_count
     FROM districts d WHERE d.state_id=? ORDER BY d.sort_order, d.name", [$stateId]
) : [];
$talukas = $districtId ? Database::all(
    "SELECT * FROM talukas WHERE district_id=? ORDER BY sort_order, name", [$districtId]
) : [];

$selectedState    = $stateId ? Database::one("SELECT * FROM states WHERE id=?", [$stateId]) : null;
$selectedDistrict = $districtId ? Database::one("SELECT * FROM districts WHERE id=?", [$districtId]) : null;
?>
<p class="text-muted small mb-4">Master data behind the State &rarr; District &rarr; Taluka dropdowns used on the membership form. Deactivating a location hides it from public dropdowns without deleting historical member records that reference it.</p>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">States / UTs (<?= count($states) ?>)</h6>
      </div>
      <form method="post" class="row g-2 mb-3">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <input type="hidden" name="level" value="state">
        <div class="col-7"><input class="form-control form-control-sm" name="name" placeholder="New state/UT name" required></div>
        <div class="col-3"><input class="form-control form-control-sm" name="code" placeholder="Code" maxlength="10"></div>
        <div class="col-2"><button class="btn btn-sm btn-blue w-100">Add</button></div>
      </form>
      <div class="table-responsive" style="max-height:520px;overflow-y:auto;">
        <table class="table table-admin align-middle table-sm">
          <tbody>
            <?php foreach ($states as $s): ?>
            <tr class="<?= $s['id'] == $stateId ? 'table-active' : '' ?>">
              <td>
                <a href="<?= admin_url('index.php?page=locations&state_id=' . $s['id']) ?>" class="fw-semibold text-decoration-none">
                  <?= e($s['name']) ?>
                </a>
                <div class="small text-muted"><?= (int) $s['district_count'] ?> districts</div>
              </td>
              <td class="text-end text-nowrap">
                <span class="badge-type <?= $s['status'] === 'active' ? 'badge-green' : 'bg-danger-subtle text-danger' ?>"><?= e(ucfirst($s['status'])) ?></span>
                <form method="post" action="<?= admin_url('index.php?page=locations&action=toggle&level=state&id=' . $s['id']) ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <button class="btn btn-sm btn-outline-secondary" title="Toggle status"><i class="fa-solid fa-toggle-on"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">
        Districts<?= $selectedState ? ' in ' . e($selectedState['name']) : '' ?>
        <?= $selectedState ? '(' . count($districts) . ')' : '' ?>
      </h6>
      <?php if (!$selectedState): ?>
        <p class="text-muted small">Select a state to view/manage its districts.</p>
      <?php else: ?>
        <form method="post" class="row g-2 mb-3">
          <?= csrf_field() ?>
          <input type="hidden" name="do" value="save">
          <input type="hidden" name="level" value="district">
          <input type="hidden" name="state_id" value="<?= (int) $stateId ?>">
          <input type="hidden" name="redirect_state" value="<?= (int) $stateId ?>">
          <div class="col-7"><input class="form-control form-control-sm" name="name" placeholder="New district name" required></div>
          <div class="col-3"><input class="form-control form-control-sm" name="code" placeholder="Code" maxlength="10"></div>
          <div class="col-2"><button class="btn btn-sm btn-blue w-100">Add</button></div>
        </form>
        <div class="table-responsive" style="max-height:480px;overflow-y:auto;">
          <table class="table table-admin align-middle table-sm">
            <tbody>
              <?php foreach ($districts as $d): ?>
              <tr class="<?= $d['id'] == $districtId ? 'table-active' : '' ?>">
                <td>
                  <a href="<?= admin_url('index.php?page=locations&state_id=' . $stateId . '&district_id=' . $d['id']) ?>" class="fw-semibold text-decoration-none">
                    <?= e($d['name']) ?>
                  </a>
                  <div class="small text-muted"><?= (int) $d['taluka_count'] ?> talukas</div>
                </td>
                <td class="text-end text-nowrap">
                  <span class="badge-type <?= $d['status'] === 'active' ? 'badge-green' : 'bg-danger-subtle text-danger' ?>"><?= e(ucfirst($d['status'])) ?></span>
                  <form method="post" action="<?= admin_url('index.php?page=locations&action=toggle&level=district&id=' . $d['id'] . '&state_id=' . $stateId) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-secondary" title="Toggle status"><i class="fa-solid fa-toggle-on"></i></button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$districts): ?><tr><td colspan="2" class="text-center text-muted py-3">No districts yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">
        Talukas<?= $selectedDistrict ? ' in ' . e($selectedDistrict['name']) : '' ?>
        <?= $selectedDistrict ? '(' . count($talukas) . ')' : '' ?>
      </h6>
      <?php if (!$selectedDistrict): ?>
        <p class="text-muted small">Select a district to view/manage its talukas.</p>
      <?php else: ?>
        <form method="post" class="row g-2 mb-3">
          <?= csrf_field() ?>
          <input type="hidden" name="do" value="save">
          <input type="hidden" name="level" value="taluka">
          <input type="hidden" name="district_id" value="<?= (int) $districtId ?>">
          <input type="hidden" name="redirect_state" value="<?= (int) $stateId ?>">
          <input type="hidden" name="redirect_district" value="<?= (int) $districtId ?>">
          <div class="col-7"><input class="form-control form-control-sm" name="name" placeholder="New taluka name" required></div>
          <div class="col-3"><input class="form-control form-control-sm" name="code" placeholder="Code" maxlength="10"></div>
          <div class="col-2"><button class="btn btn-sm btn-blue w-100">Add</button></div>
        </form>
        <div class="table-responsive" style="max-height:480px;overflow-y:auto;">
          <table class="table table-admin align-middle table-sm">
            <tbody>
              <?php foreach ($talukas as $t): ?>
              <tr>
                <td><?= e($t['name']) ?></td>
                <td class="text-end text-nowrap">
                  <span class="badge-type <?= $t['status'] === 'active' ? 'badge-green' : 'bg-danger-subtle text-danger' ?>"><?= e(ucfirst($t['status'])) ?></span>
                  <form method="post" action="<?= admin_url('index.php?page=locations&action=toggle&level=taluka&id=' . $t['id'] . '&state_id=' . $stateId . '&district_id=' . $districtId) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-secondary" title="Toggle status"><i class="fa-solid fa-toggle-on"></i></button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$talukas): ?><tr><td colspan="2" class="text-center text-muted py-3">No talukas yet - add them above, or leave empty so applicants must use "Other".</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
