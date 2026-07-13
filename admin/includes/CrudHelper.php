<?php
/**
 * SimpleCrud - a tiny, config-driven list/add/edit/delete helper for admin
 * modules that manage a single table. Complex modules (members, donations,
 * events+registrations, settings) are written by hand instead of using this.
 *
 * Field types: text, textarea, number, date, select, image, file, checkbox
 */
class SimpleCrud
{
    public function __construct(
        private string $table,
        private array $fields,      // ['name' => ['label'=>'', 'type'=>'text', 'required'=>true, 'options'=>[...], 'subdir'=>'gallery']]
        private string $orderBy = 'id DESC',
        private string $listLabelField = 'title',
        private ?string $slugFrom = null   // e.g. 'title' -> auto-fill 'slug' field from this field when left blank
    ) {}

    public function handle(string $pageTitle): void
    {
        $action = get_param('action', 'list');
        $id = (int) get_param('id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'save') {
            require_csrf();
            $this->save($id);
            redirect('admin/index.php?page=' . get_param('page'));
        }

        if ($action === 'delete' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $row = Database::one("SELECT * FROM `{$this->table}` WHERE id=?", [$id]);
            foreach ($this->fields as $key => $f) {
                if (in_array($f['type'], ['image','file'], true) && !empty($row[$key])) {
                    delete_upload($row[$key]);
                }
            }
            Database::delete($this->table, 'id=?', [$id]);
            flash_set('success', 'Deleted successfully.');
            redirect('admin/index.php?page=' . get_param('page'));
        }

        $editRow = null;
        if ($action === 'edit' && $id) {
            $editRow = Database::one("SELECT * FROM `{$this->table}` WHERE id=?", [$id]);
        }

        $rows = Database::all("SELECT * FROM `{$this->table}` ORDER BY {$this->orderBy}");
        $this->renderList($pageTitle, $rows, $editRow);
    }

    private function save(int $id): void
    {
        $data = [];
        foreach ($this->fields as $key => $f) {
            if ($f['type'] === 'checkbox') {
                $data[$key] = isset($_POST[$key]) ? 1 : 0;
                continue;
            }
            if ($f['type'] === 'image' || $f['type'] === 'file') {
                try {
                    $allowed = $f['type'] === 'image' ? ALLOWED_IMAGE_TYPES : ALLOWED_DOC_TYPES;
                    $uploaded = handle_upload($key, $f['subdir'] ?? 'misc', $allowed);
                    if ($uploaded) {
                        if ($id) delete_upload(Database::value("SELECT `$key` FROM `{$this->table}` WHERE id=?", [$id]));
                        $data[$key] = $uploaded;
                    }
                } catch (RuntimeException $e) {
                    flash_set('error', $e->getMessage());
                }
                continue;
            }
            $val = post($key);
            if ($f['type'] === 'number') $val = $val === '' ? null : $val;
            $data[$key] = $val;
        }

        if ($this->slugFrom && array_key_exists('slug', $data) && $data['slug'] === '') {
            $data['slug'] = unique_slug($this->table, $data[$this->slugFrom] ?? 'item', $id);
        }

        if ($id) {
            Database::update($this->table, $data, 'id=?', [$id]);
            flash_set('success', 'Updated successfully.');
        } else {
            Database::insert($this->table, $data);
            flash_set('success', 'Added successfully.');
        }
    }

    private function renderList(string $pageTitle, array $rows, ?array $editRow): void
    {
        $page = get_param('page');
        ?>
        <div class="row g-4">
          <div class="col-lg-5">
            <div class="admin-card">
              <h6 class="fw-bold mb-3"><?= $editRow ? 'Edit entry' : 'Add new' ?></h6>
              <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="do" value="save">
                <?php foreach ($this->fields as $key => $f): $val = $editRow[$key] ?? ($f['default'] ?? ''); ?>
                  <div class="mb-3">
                    <label class="form-label"><?= e($f['label']) ?></label>
                    <?php if ($f['type'] === 'textarea'): ?>
                      <textarea class="form-control" name="<?= e($key) ?>" rows="<?= $f['rows'] ?? 3 ?>" <?= !empty($f['required']) ? 'required' : '' ?>><?= e($val) ?></textarea>
                    <?php elseif ($f['type'] === 'select'): ?>
                      <select class="form-select" name="<?= e($key) ?>" <?= !empty($f['required']) ? 'required' : '' ?>>
                        <?php foreach ($f['options'] as $ok => $ov): ?>
                          <option value="<?= e((string)$ok) ?>" <?= (string) $val === (string) $ok ? 'selected' : '' ?>><?= e($ov) ?></option>
                        <?php endforeach; ?>
                      </select>
                    <?php elseif ($f['type'] === 'checkbox'): ?>
                      <div class="form-check"><input type="checkbox" class="form-check-input" name="<?= e($key) ?>" value="1" <?= $val ? 'checked' : '' ?>></div>
                    <?php elseif ($f['type'] === 'image' || $f['type'] === 'file'): ?>
                      <?php if ($val): ?><div class="small text-muted mb-1"><?= $f['type']==='image' ? '<img src="'.e(upload_url($val)).'" class="thumb-sm mb-1">' : e(basename($val)) ?></div><?php endif; ?>
                      <input type="file" class="form-control" name="<?= e($key) ?>" accept="<?= $f['type']==='image' ? 'image/*' : '.pdf,.doc,.docx' ?>">
                    <?php else: ?>
                      <input type="<?= e($f['type']) ?>" class="form-control" name="<?= e($key) ?>" value="<?= e((string)$val) ?>" <?= !empty($f['required']) ? 'required' : '' ?> <?= isset($f['placeholder']) ? 'placeholder="'.e($f['placeholder']).'"' : '' ?>>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
                <button class="btn btn-blue" type="submit"><?= $editRow ? 'Update' : 'Add' ?></button>
                <?php if ($editRow): ?><a href="<?= admin_url('index.php?page=' . $page) ?>" class="btn btn-outline-nav">Cancel</a><?php endif; ?>
              </form>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="admin-card">
              <h6 class="fw-bold mb-3">All entries (<?= count($rows) ?>)</h6>
              <div class="table-responsive">
                <table class="table table-admin align-middle">
                  <thead><tr><th>#</th><th><?= e(ucfirst($this->listLabelField)) ?></th><th></th></tr></thead>
                  <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                      <td><?= (int) $r['id'] ?></td>
                      <td><?= e((string) ($r[$this->listLabelField] ?? '')) ?></td>
                      <td class="text-end">
                        <a href="<?= admin_url('index.php?page=' . $page . '&action=edit&id=' . $r['id']) ?>" class="btn btn-sm btn-outline-nav"><i class="fa-solid fa-pen"></i></a>
                        <form method="post" action="<?= admin_url('index.php?page=' . $page . '&action=delete&id=' . $r['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this entry?');">
                          <?= csrf_field() ?>
                          <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?><tr><td colspan="3" class="text-center text-muted py-4">No entries yet.</td></tr><?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php
    }
}
