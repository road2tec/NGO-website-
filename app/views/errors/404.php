<section class="section min-vh-60 d-flex align-items-center">
  <div class="container text-center">
    <div class="eyebrow justify-content-center mb-3">Error 404</div>
    <h1 class="display-font mb-3">We couldn't find that page</h1>
    <p class="text-muted mb-4"><?= e($message ?? 'The page you are looking for may have moved or no longer exists.') ?></p>
    <a href="<?= url('') ?>" class="btn btn-blue">Back to homepage</a>
  </div>
</section>
