<?php
/**
 * Reusable CAPTCHA form field. Include after setting an optional
 * $captchaFieldId (defaults to "captcha"); the image itself generates and
 * stores the answer server-side the moment the browser requests it, so
 * nothing needs to be passed in from the controller.
 */
$captchaFieldId = $captchaFieldId ?? 'captcha';
?>
<label class="form-label" for="<?= e($captchaFieldId) ?>">Security check <span class="text-danger">*</span></label>
<div class="captcha-widget">
  <div class="captcha-image-wrap">
    <img src="<?= url('captcha-image.php') ?>?t=<?= time() ?>" alt="CAPTCHA image - enter the characters shown" class="captcha-img" width="170" height="60">
    <button type="button" class="captcha-refresh-btn" aria-label="Get a new captcha image" title="Refresh captcha">
      <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i>
    </button>
  </div>
  <input type="text" class="form-control captcha-input" id="<?= e($captchaFieldId) ?>" name="captcha"
         placeholder="Enter the code shown" autocomplete="off" autocapitalize="characters" spellcheck="false" required>
</div>
