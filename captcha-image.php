<?php
/**
 * Streams a fresh, dynamically generated CAPTCHA challenge as a PNG image and
 * stores the expected answer server-side in the session. Not routed through
 * the front controller - loaded directly by <img src="captcha-image.php">,
 * same pattern as /api/index.php.
 */
require_once __DIR__ . '/config/config.php';
captcha_stream_image();
