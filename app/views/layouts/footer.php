</main>

<!-- ======= Floating donate button ======= -->
<a href="<?= url('donate') ?>" class="floating-donate" aria-label="Donate now">
  <i class="fa-solid fa-heart" aria-hidden="true"></i><span>Donate</span>
</a>

<!-- ======= Footer ======= -->
<footer class="site-footer">
  <div class="seva-band" aria-hidden="true"></div>
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand mb-3">
          <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-hands-holding-child"></i></span>
          <span class="brand-text"><?= e(setting('site_name')) ?></span>
        </div>
        <p class="footer-text"><?= e(setting('site_tagline')) ?>. Registration No: <?= e(setting('registration_no')) ?>. <?= e(setting('pan_80g')) ?>.</p>
        <div class="footer-social d-flex gap-2 mt-3">
          <a href="<?= e(setting('facebook_url')) ?>" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="<?= e(setting('instagram_url')) ?>" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="<?= e(setting('twitter_url')) ?>" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="<?= e(setting('youtube_url')) ?>" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-head">Explore</h6>
        <ul class="footer-links">
          <li><a href="<?= url('about') ?>">About Us</a></li>
          <li><a href="<?= url('projects') ?>">Projects</a></li>
          <li><a href="<?= url('activities') ?>">Activities</a></li>
          <li><a href="<?= url('gallery') ?>">Gallery</a></li>
          <li><a href="<?= url('news') ?>">News</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-head">Get involved</h6>
        <ul class="footer-links">
          <li><a href="<?= url('membership/apply') ?>">Become a member</a></li>
          <li><a href="<?= url('volunteer') ?>">Volunteer</a></li>
          <li><a href="<?= url('donate') ?>">Donate</a></li>
          <li><a href="<?= url('donate/campaigns') ?>">Crowdfunding</a></li>
          <li><a href="<?= url('certificate') ?>">Event certificate</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-head">Legal</h6>
        <ul class="footer-links">
          <li><a href="<?= url('page/privacy') ?>">Privacy Policy</a></li>
          <li><a href="<?= url('page/terms') ?>">Terms &amp; Conditions</a></li>
          <li><a href="<?= url('page/refund') ?>">Refund Policy</a></li>
          <li><a href="<?= url('page/disclaimer') ?>">Disclaimer</a></li>
          <li><a href="<?= url('documents') ?>">Documents</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-head">Contact</h6>
        <ul class="footer-links footer-contact">
          <li><i class="fa-solid fa-location-dot me-2" aria-hidden="true"></i><?= e(setting('site_address')) ?></li>
          <li><i class="fa-solid fa-phone me-2" aria-hidden="true"></i><?= e(setting('site_phone')) ?></li>
          <li><i class="fa-solid fa-envelope me-2" aria-hidden="true"></i><?= e(setting('site_email')) ?></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3">
      <span>&copy; <?= date('Y') ?> <?= e(setting('site_name')) ?>. All rights reserved.</span>
      <form class="d-flex gap-2" method="post" action="<?= url('newsletter') ?>">
        <?= csrf_field() ?>
        <label class="visually-hidden" for="nl-email">Email for newsletter</label>
        <input type="email" id="nl-email" name="email" class="form-control form-control-sm footer-nl" placeholder="Your email for updates" required>
        <button class="btn btn-sm btn-donate" type="submit">Subscribe</button>
      </form>
    </div>
  </div>
</footer>

<!-- ======= Scripts ======= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="<?= asset('js/main.js') ?>" defer></script>
<!-- Google Translate (English / Hindi / Marathi + more) -->
<script>
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
    includedLanguages: 'en,hi,mr,gu,kn,ta,te,bn',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}
</script>
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" defer></script>
</body>
</html>
