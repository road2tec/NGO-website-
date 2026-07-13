<?php
/**
 * Reserved for future REST/JSON endpoints (e.g. a mobile app or
 * headless integration). Not used by the website itself, which is
 * rendered server-side through app/controllers + app/views.
 */
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'No API endpoints are defined yet.']);
