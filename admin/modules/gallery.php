<?php
$sub = get_param('tab', 'items');

/* ---- Album quick add/delete ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'save_album') {
    require_csrf();
    Database::insert('gallery_albums', ['title' => post('album_title'), 'sort_order' => (int) post('album_sort')]);
    flash_set('success', 'Album added.');
    redirect('admin/index.php?page=gallery&tab=albums');
}
if (get_param('action') === 'delete_album' && get_param('id') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    Database::delete('gallery_albums', 'id=?', [(int) get_param('id')]);
    flash_set('success', 'Album deleted.');
    redirect('admin/index.php?page=gallery&tab=albums');
}

/* ---- Item add/delete ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('do') === 'save_item') {
    require_csrf();
    $type = post('type');
    $data = [
        'album_id' => (int) post('album_id') ?: null,
        'type'     => $type,
        'caption'  => post('caption'),
    ];
    if ($type === 'video') {
        $data['youtube_id'] = post('youtube_id');
    } else {
        try {
            $data['file'] = handle_upload('file', 'gallery');
        } catch (RuntimeException $e) { flash_set('error', $e->getMessage()); redirect('admin/index.php?page=gallery'); }
    }
    Database::insert('gallery_items', $data);
    flash_set('success', 'Gallery item added.');
    redirect('admin/index.php?page=gallery');
}
if (get_param('action') === 'delete_item' && get_param('id') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = (int) get_param('id');
    delete_upload(Database::value("SELECT file FROM gallery_items WHERE id=?", [$id]));
    Database::delete('gallery_items', 'id=?', [$id]);
    flash_set('success', 'Item deleted.');
    redirect('admin/index.php?page=gallery');
}

$albums = Database::all("SELECT * FROM gallery_albums ORDER BY sort_order");
$items  = Database::all("SELECT gi.*, ga.title AS album_title FROM gallery_items gi LEFT JOIN gallery_albums ga ON ga.id=gi.album_id ORDER BY gi.id DESC");
?>
<ul class="nav nav-pills mb-4">
  <li class="nav-item"><a class="nav-link <?= $sub==='items'?'active':'' ?>" href="<?= admin_url('index.php?page=gallery&tab=items') ?>">Images &amp; Videos</a></li>
  <li class="nav-item"><a class="nav-link <?= $sub==='albums'?'active':'' ?>" href="<?= admin_url('index.php?page=gallery&tab=albums') ?>">Albums</a></li>
</ul>

<?php if ($sub === 'albums'): ?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">Add album</h6>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="do" value="save_album">
        <div class="mb-3"><label class="form-label">Album title</label><input class="form-control" name="album_title" required></div>
        <div class="mb-3"><label class="form-label">Sort order</label><input type="number" class="form-control" name="album_sort" value="0"></div>
        <button class="btn btn-blue">Add album</button>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">Albums (<?= count($albums) ?>)</h6>
      <table class="table table-admin"><tbody>
        <?php foreach ($albums as $a): ?>
        <tr><td><?= e($a['title']) ?></td><td class="text-end">
          <form method="post" action="<?= admin_url('index.php?page=gallery&tab=albums&action=delete_album&id=' . $a['id']) ?>" onsubmit="return confirm('Delete album?');" class="d-inline">
            <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
          </form>
        </td></tr>
        <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
</div>
<?php else: ?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">Add image / video</h6>
      <form method="post" enctype="multipart/form-data" id="gForm"><?= csrf_field() ?><input type="hidden" name="do" value="save_item">
        <div class="mb-3">
          <label class="form-label">Type</label>
          <select class="form-select" name="type" id="gType" onchange="document.getElementById('gImgWrap').classList.toggle('d-none', this.value==='video'); document.getElementById('gVidWrap').classList.toggle('d-none', this.value==='image');">
            <option value="image">Image</option><option value="video">YouTube Video</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Album (optional)</label>
          <select class="form-select" name="album_id">
            <option value="">No album</option>
            <?php foreach ($albums as $a): ?><option value="<?= (int) $a['id'] ?>"><?= e($a['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3" id="gImgWrap"><label class="form-label">Image file</label><input type="file" class="form-control" name="file" accept="image/*"></div>
        <div class="mb-3 d-none" id="gVidWrap"><label class="form-label">YouTube video ID</label><input class="form-control" name="youtube_id" placeholder="e.g. dQw4w9WgXcQ"></div>
        <div class="mb-3"><label class="form-label">Caption</label><input class="form-control" name="caption"></div>
        <button class="btn btn-blue">Add</button>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="admin-card">
      <h6 class="fw-bold mb-3">All items (<?= count($items) ?>)</h6>
      <div class="table-responsive">
        <table class="table table-admin align-middle">
          <thead><tr><th>Preview</th><th>Type</th><th>Album</th><th>Caption</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
              <td><?= $it['type']==='image' ? '<img src="'.e(upload_url($it['file'])).'" class="thumb-sm">' : '<i class="fa-brands fa-youtube fa-lg text-danger"></i>' ?></td>
              <td><?= e(ucfirst($it['type'])) ?></td>
              <td><?= e($it['album_title'] ?? '—') ?></td>
              <td><?= e($it['caption']) ?></td>
              <td class="text-end">
                <form method="post" action="<?= admin_url('index.php?page=gallery&action=delete_item&id=' . $it['id']) ?>" onsubmit="return confirm('Delete item?');" class="d-inline">
                  <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
