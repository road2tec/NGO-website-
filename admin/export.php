<?php
/**
 * Authenticated bulk data export (CSV/XLSX) for the Donations and Members
 * admin pages. Not wrapped in the admin layout - streams a file and exits,
 * same pattern as download.php.
 */
require_once __DIR__ . '/includes/auth.php';
require_admin();

$type   = get_param('type');
$format = get_param('format', 'csv') === 'xlsx' ? 'xlsx' : 'csv';

if ($type === 'donors') {
    $rows = Database::all(
        "SELECT d.donor_name, d.phone, d.email, d.pan, d.amount, c.title AS campaign_title, d.created_at, d.method
         FROM donations d LEFT JOIN campaigns c ON c.id = d.campaign_id
         WHERE d.status = 'received' ORDER BY d.created_at DESC"
    );
    $headers = ['Name', 'Mobile', 'Email', 'PAN', 'Amount', 'Campaign', 'Date', 'Payment Mode'];
    $data = array_map(fn($r) => [
        $r['donor_name'], $r['phone'], $r['email'], $r['pan'] ?: '',
        $r['amount'], $r['campaign_title'] ?: 'General Fund',
        format_date($r['created_at'], 'd-m-Y'), strtoupper($r['method']),
    ], $rows);
    $filename = 'donor-list-' . date('Y-m-d');
} elseif ($type === 'members') {
    $rows = Database::all(
        "SELECT m.name, m.phone, m.email, st.name AS state_name, d.name AS district_name, t.name AS taluka_name
         FROM members m
         LEFT JOIN states st ON st.id = m.state_id
         LEFT JOIN districts d ON d.id = m.district_id
         LEFT JOIN talukas t ON t.id = m.taluka_id
         WHERE m.status = 'approved' ORDER BY m.name"
    );
    $headers = ['Name', 'Mobile', 'Email', 'State', 'District', 'Taluka'];
    $data = array_map(fn($r) => [
        $r['name'], $r['phone'], $r['email'],
        $r['state_name'] ?: '', $r['district_name'] ?: '', $r['taluka_name'] ?: '',
    ], $rows);
    $filename = 'member-list-' . date('Y-m-d');
} else {
    http_response_code(404);
    die('Unknown export type.');
}

if ($format === 'xlsx') {
    export_xlsx($filename, $headers, $data);
} else {
    export_csv($filename, $headers, $data);
}
