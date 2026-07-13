<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item active">Gallery</li></ol></nav>
    <h1 class="mt-2">Gallery</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="gallery-filter d-flex flex-wrap gap-2 mb-4">
      <button class="btn btn-blue btn-sm" data-filter="all">All</button>
      <?php foreach ($albums as $a): ?>
        <button class="btn btn-outline-nav btn-sm" data-filter="<?= (int) $a['id'] ?>"><?= e($a['title']) ?></button>
      <?php endforeach; ?>
    </div>

    <?php if (empty($images)): ?>
      <p class="text-muted">No images uploaded yet.</p>
    <?php else: ?>
    <div class="row g-3 mb-5">
      <?php foreach ($images as $g): ?>
      <div class="col-6 col-md-4 col-lg-3" data-album="<?= (int) $g['album_id'] ?>" data-aos="fade-up">
        <a href="#" class="gallery-item" data-full="<?= e(upload_url($g['file'])) ?>" data-caption="<?= e($g['caption']) ?>">
          <img src="<?= e(upload_url($g['file'])) ?>" alt="<?= e($g['caption']) ?>">
          <?php if ($g['caption']): ?><span class="cap"><?= e($g['caption']) ?></span><?php endif; ?>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($videos): ?>
    <h4 class="fw-bold mb-4">Videos</h4>
    <div class="row g-4">
      <?php foreach ($videos as $v): ?>
      <div class="col-md-6" data-aos="fade-up">
        <div class="video-frame">
          <iframe src="https://www.youtube.com/embed/<?= e($v['youtube_id']) ?>" title="<?= e($v['caption']) ?>" allowfullscreen loading="lazy"></iframe>
        </div>
        <?php if ($v['caption']): ?><p class="small text-muted mt-2"><?= e($v['caption']) ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<div class="lightbox" id="lightbox"><button class="close-btn" aria-label="Close">&times;</button><img src="" alt=""></div>
