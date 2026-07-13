<?php
$action = get_param('action', 'list');
$id = (int) get_param('id');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'save') {
    require_csrf();
    $slug = post('slug') ?: unique_slug('events', post('title'), $id);
    $data = [
        'title' => post('title'), 'slug' => $slug,
        'type' => post('type'), 'description' => post('description'),
        'venue' => post('venue'), 'event_date' => post('event_date'),
        'event_time' => post('event_time'),
        'registration_open' => isset($_POST['registration_open']) ? 1 : 0,
    ];
    try {
        $img = handle_upload('image', 'activities');
        if ($img) {
            if ($id) delete_upload(Database::value("SELECT image FROM events WHERE id=?", [$id]));
            $data['image'] = $img;
        }
    } catch (RuntimeException $e) { flash_set('error', $e->getMessage()); }

    if ($id) {
        Database::update('events', $data, 'id=?', [$id]);
        flash_set('success', 'Event updated.');
    } else {
        $data['event_code'] = 'EVT-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
        Database::insert('events', $data);
        flash_set('success', 'Event created. Event ID: ' . $data['event_code']);
    }
    redirect('admin/index.php?page=events');
}

if ($action === 'delete' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    delete_upload(Database::value("SELECT image FROM events WHERE id=?", [$id]));
    Database::delete('events', 'id=?', [$id]);
    flash_set('success', 'Event deleted.');
    redirect('admin/index.php?page=events');
}

if ($action === 'mark_attended' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::update('event_registrations', ['attended' => 1], 'id=?', [$id]);
    flash_set('success', 'Marked as attended. The participant can now download their certificate.');
    redirect('admin/index.php?page=events&action=registrations&event_id=' . get_param('event_id'));
}

if ($action === 'registrations') {
    $eventId = (int) get_param('event_id');
    $event = Database::one("SELECT * FROM events WHERE id=?", [$eventId]);
    $regs = Database::all("SELECT * FROM event_registrations WHERE event_id=? ORDER BY id DESC", [$eventId]);
    ?>
    <div class="admin-card mb-3 d-flex justify-content-between align-items-center">
      <div><strong><?= e($event['title'] ?? '') ?></strong> <span class="text-muted small">(<?= e($event['event_code'] ?? '') ?>)</span></div>
      <a href="<?= admin_url('index.php?page=events') ?>" class="btn btn-outline-nav btn-sm">Back to events</a>
    </div>
    <div class="admin-card">
      <h6 class="fw-bold mb-3">Registrations (<?= count($regs) ?>)</h6>
      <div class="table-responsive">
        <table class="table table-admin">
          <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Attended</th><th>Cert. code</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($regs as $r): ?>
            <tr>
              <td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['phone']) ?></td>
              <td><?= $r['attended'] ? '<span class="badge-type badge-green">Yes</span>' : '<span class="badge-type badge-orange">No</span>' ?></td>
              <td><?= $r['cert_code'] ? '<code>'.e($r['cert_code']).'</code>' : '—' ?></td>
              <td>
                <?php if (!$r['attended']): ?>
                <form method="post" action="<?= admin_url('index.php?page=events&action=mark_attended&id=' . $r['id'] . '&event_id=' . $eventId) ?>">
                  <?= csrf_field() ?><button class="btn btn-sm btn-blue">Mark attended</button>
                </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$regs): ?><tr><td colspan="6" class="text-center text-muted py-3">No registrations yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
    return;
}

$editRow = ($action === 'edit' && $id) ? Database::one("SELECT * FROM events WHERE id=?", [$id]) : null;
$rows = Database::all("SELECT * FROM events ORDER BY event_date DESC");
$regCounts = [];
foreach (Database::all("SELECT event_id, COUNT(*) c FROM event_registrations GROUP BY event_id") as $rc) {
    $regCounts[$rc['event_id']] = (int) $rc['c'];
}
?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="admin-card">
      <h6 class="fw-bold mb-3"><?= $editRow ? 'Edit event' : 'Add new event' ?></h6>
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title" value="<?= e($editRow['title'] ?? '') ?>" required></div>
        <div class="mb-3"><label class="form-label">Slug (optional)</label><input class="form-control" name="slug" value="<?= e($editRow['slug'] ?? '') ?>"></div>
        <div class="mb-3">
          <label class="form-label">Type</label>
          <select class="form-select" name="type" required>
            <?php foreach (['activity'=>'General Activity','workshop'=>'Workshop','awareness'=>'Awareness Program','training'=>'Training','blood_donation'=>'Blood Donation','tree_plantation'=>'Tree Plantation'] as $k=>$v): ?>
              <option value="<?= $k ?>" <?= ($editRow['type'] ?? '')===$k?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4"><?= e($editRow['description'] ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Image</label><input type="file" class="form-control" name="image" accept="image/*"></div>
        <div class="mb-3"><label class="form-label">Venue</label><input class="form-control" name="venue" value="<?= e($editRow['venue'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Date</label><input type="date" class="form-control" name="event_date" value="<?= e($editRow['event_date'] ?? '') ?>" required></div>
        <div class="mb-3"><label class="form-label">Time</label><input class="form-control" name="event_time" value="<?= e($editRow['event_time'] ?? '') ?>" placeholder="e.g. 9:00 AM - 4:00 PM"></div>
        <div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="registration_open" <?= ($editRow['registration_open'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label">Registration open</label></div>
        <button class="btn btn-blue" type="submit"><?= $editRow ? 'Update' : 'Add' ?></button>
        <?php if ($editRow): ?><a href="<?= admin_url('index.php?page=events') ?>" class="btn btn-outline-nav">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">All events (<?= count($rows) ?>)</h6>
      <div class="table-responsive">
        <table class="table table-admin align-middle">
          <thead><tr><th>Title</th><th>Date</th><th>Event ID</th><th>Regs</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= e($r['title']) ?></td>
              <td><?= format_date($r['event_date']) ?></td>
              <td><code><?= e($r['event_code']) ?></code></td>
              <td><a href="<?= admin_url('index.php?page=events&action=registrations&event_id=' . $r['id']) ?>"><?= $regCounts[$r['id']] ?? 0 ?></a></td>
              <td class="text-end">
                <a href="<?= admin_url('index.php?page=events&action=edit&id=' . $r['id']) ?>" class="btn btn-sm btn-outline-nav"><i class="fa-solid fa-pen"></i></a>
                <form method="post" action="<?= admin_url('index.php?page=events&action=delete&id=' . $r['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this event?');">
                  <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">No events yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
