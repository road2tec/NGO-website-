<?php
/**
 * Small JSON API. Currently serves the dependent location dropdowns
 * (state -> district -> taluka); more endpoints can be added the same way.
 * Not routed through the front controller - hit directly as /api/index.php.
 */
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$resource = get_param('resource');

try {
    switch ($resource) {
        case 'districts':
            $stateId = (int) get_param('state_id');
            if ($stateId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'state_id is required.']);
                break;
            }
            echo json_encode(['districts' => location_districts($stateId)]);
            break;

        case 'talukas':
            $districtId = (int) get_param('district_id');
            if ($districtId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'district_id is required.']);
                break;
            }
            echo json_encode(['talukas' => location_talukas($districtId)]);
            break;

        case 'job_subcategories':
            $categoryId = (int) get_param('category_id');
            if ($categoryId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'category_id is required.']);
                break;
            }
            echo json_encode(['subcategories' => job_subcategories($categoryId)]);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Unknown resource.']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Something went wrong. Please try again.']);
}
