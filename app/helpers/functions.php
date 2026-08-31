<?php
/**
 * Global helper functions used across the public site and admin panel.
 */

/* ---------------- Output escaping (XSS protection) ---------------- */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* ---------------- URLs ---------------- */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Appends a filemtime-based ?v= so every deploy auto-busts the browser/host cache on CSS/JS - no more "did my fix even go live?" confusion. */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $localFile = dirname(__DIR__, 2) . '/assets/' . $path;
    $version = is_file($localFile) ? '?v=' . filemtime($localFile) : '';
    return BASE_URL . '/assets/' . $path . $version;
}

/**
 * Admin-entered links (banner buttons, homepage buttons, ...) can be an
 * internal relative path ("donate") or a full external URL
 * ("https://example.com"). Route both correctly and never render a
 * javascript:/data:/vbscript: scheme.
 */
function is_external_url(string $link): bool
{
    $link = trim($link);
    return $link !== '' && ((bool) preg_match('#^https?://#i', $link) || str_starts_with($link, '//'));
}

function is_unsafe_url_scheme(string $link): bool
{
    $lower = strtolower(trim($link));
    foreach (['javascript:', 'data:', 'vbscript:'] as $scheme) {
        if (str_starts_with($lower, $scheme)) return true;
    }
    return false;
}

function safe_link_url(string $link): string
{
    if (is_unsafe_url_scheme($link)) return '#';
    return is_external_url($link) ? $link : url($link);
}

/** HTML attributes to append to an <a> tag so external links open safely in a new tab. */
function link_target_attrs(string $link): string
{
    if (is_unsafe_url_scheme($link)) return '';
    return is_external_url($link) ? ' target="_blank" rel="noopener noreferrer"' : '';
}

function upload_url(?string $path, string $fallback = 'images/placeholder.svg'): string
{
    if (!empty($path) && file_exists(UPLOAD_DIR . '/' . $path)) {
        return UPLOAD_URL . '/' . $path;
    }
    return asset($fallback);
}

function redirect(string $path): void
{
    // Discard any buffered output (e.g. the admin layout's ob_start() wrapper)
    // so a mid-page redirect doesn't also ship a half-rendered body.
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Location: ' . url($path));
    exit;
}

/* ---------------- Settings (cached key/value store) ---------------- */
function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (Database::all("SELECT setting_key, setting_value FROM settings") as $row) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) { /* table missing during install */ }
    }
    return $cache[$key] ?? $default;
}

function save_setting(string $key, string $value): void
{
    Database::query(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?,?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
        [$key, $value]
    );
}

/* ---------------- Email (SMTP via PHPMailer - vendored, no paid service, no Composer) ---------------- */
/**
 * Sends an email through the SMTP server configured in Admin -> Settings -> Email.
 * Falls back to PHP's native mail() only when no SMTP host has been configured yet,
 * since that is unreliable on most shared hosting and should not be relied on long-term.
 * Returns whether the send actually succeeded - callers should not assume success.
 */
function send_mail(string $to, string $subject, string $body, ?string $toName = null, array $attachments = []): bool
{
    $fromEmail = setting('smtp_from_email') ?: setting('site_email');
    $fromName  = setting('smtp_from_name') ?: setting('site_name');

    if (!setting('smtp_host')) {
        if ($attachments) {
            // Raw mail() attachments would need hand-rolled MIME multipart -
            // not worth the fragility. Configure SMTP above to send documents.
            error_log('send_mail: attachments require SMTP to be configured (Admin -> Settings -> Email).');
            return false;
        }
        $headers = "From: $fromName <$fromEmail>\r\nContent-Type: text/plain; charset=UTF-8";
        return @mail($to, $subject, $body, $headers);
    }

    require_once dirname(__DIR__) . '/lib/PHPMailer/Exception.php';
    require_once dirname(__DIR__) . '/lib/PHPMailer/PHPMailer.php';
    require_once dirname(__DIR__) . '/lib/PHPMailer/SMTP.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = setting('smtp_host');
        $mail->SMTPAuth   = true;
        $mail->Username   = setting('smtp_username');
        $mail->Password   = setting('smtp_password');
        $mail->SMTPSecure = setting('smtp_encryption', 'tls');
        $mail->Port       = (int) setting('smtp_port', '587');
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to, (string) $toName);
        $mail->addReplyTo($fromEmail, $fromName);

        foreach ($attachments as $attachment) {
            $mail->addStringAttachment($attachment['content'], $attachment['name'], 'base64', $attachment['mime'] ?? 'application/pdf');
        }

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('send_mail failed to ' . $to . ': ' . $mail->ErrorInfo);
        return false;
    }
}

/* ---------------- Donation certificate & receipt (PDF via tFPDF - vendored, no Composer, Unicode/rupee support) ---------------- */
/** Downloads a verification QR as a temp PNG for embedding in a PDF. Returns null (skip silently) if unreachable. */
function _donation_pdf_qr(string $data): ?string
{
    $png = @file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=160x160&format=png&data=' . urlencode($data));
    if (!$png) return null;
    $path = tempnam(sys_get_temp_dir(), 'qr') . '.png';
    file_put_contents($path, $png);
    return $path;
}

/** Substitutes {{key}} placeholders with values from $vars. Unknown placeholders are left visible rather than silently dropped, so a typo'd key is easy to spot. */
function render_template(string $template, array $vars): string
{
    return preg_replace_callback('/\{\{(\w+)\}\}/', function ($m) use ($vars) {
        return array_key_exists($m[1], $vars) ? (string) $vars[$m[1]] : $m[0];
    }, $template);
}

function default_cert_message_template(): string
{
    return 'has generously donated {{amount}} to {{campaign}} on {{date}}, in support of our mission.';
}

function default_failed_payment_template(): string
{
    return "Dear {{donor_name}},\n\nWe were unable to confirm your donation pledge of {{amount}} to {{campaign}} (Ref: {{receipt_no}}).\n\n"
         . "If you already completed the transfer, please reply to this email with your payment confirmation so we can re-verify it. "
         . "You're also welcome to submit a new pledge if you'd like to try again.\n\nWith thanks,\n{{site_name}}";
}

/** Placeholder values available to both the certificate and failed-payment templates. */
function donation_template_vars(array $donation): array
{
    return [
        'donor_name' => $donation['donor_name'],
        'amount'     => format_inr($donation['amount']),
        'campaign'   => $donation['campaign_title'] ?? 'the General Fund',
        'date'       => format_date($donation['created_at'], 'd F Y'),
        'receipt_no' => $donation['receipt_no'] ?? '',
        'site_name'  => setting('site_name'),
    ];
}

/** Shared letterhead font setup for both PDFs. */
function _donation_pdf_new(string $orientation): tFPDF
{
    $fontDir = dirname(__DIR__) . '/lib/TFPDF/font/unifont/';
    foreach (['DejaVuSans.ttf', 'DejaVuSans-Bold.ttf'] as $fontFile) {
        if (!is_readable($fontDir . $fontFile)) {
            // Fails cleanly here instead of a raw die() deep inside the vendored
            // library, which otherwise crashes with no admin-page styling at all.
            throw new RuntimeException(
                "Certificate/receipt generation is unavailable: $fontFile is missing or unreadable at "
                . "app/lib/TFPDF/font/unifont/ on this server. Re-upload that folder from the deployment package."
            );
        }
    }
    require_once dirname(__DIR__) . '/lib/TFPDF/tfpdf.php';
    require_once dirname(__DIR__) . '/lib/TFPDF/font/unifont/ttfonts.php';
    $pdf = new tFPDF($orientation, 'mm', 'A4');
    $pdf->SetAutoPageBreak(false);
    $pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
    $pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);
    $pdf->AddPage();
    return $pdf;
}

/** Generates a premium landscape donation certificate as raw PDF bytes (for preview streaming or email attachment). Pass $customMessage to preview/send an edited-but-unsaved wording. */
function generate_donation_certificate_pdf(array $donation, ?string $customMessage = null): string
{
    $message = $customMessage ?? render_template(
        setting('cert_message_template') ?: default_cert_message_template(),
        donation_template_vars($donation)
    );
    $pdf = _donation_pdf_new('L');
    [$w, $h] = [297, 210];
    $blue = [20, 84, 156]; $orange = [244, 119, 46]; $green = [46, 158, 92]; $ink = [30, 40, 54]; $muted = [91, 107, 126];

    // seva band - top and bottom, matching the site's signature blue/orange/green ribbon
    foreach ([0, $h - 4] as $y) {
        $pdf->SetFillColor(...$blue);   $pdf->Rect(0, $y, $w * 0.34, 4, 'F');
        $pdf->SetFillColor(...$orange); $pdf->Rect($w * 0.34, $y, $w * 0.33, 4, 'F');
        $pdf->SetFillColor(...$green);  $pdf->Rect($w * 0.67, $y, $w * 0.33, 4, 'F');
    }
    $pdf->SetDrawColor(...$blue);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect(8, 10, $w - 16, $h - 20);

    $logo = setting('org_logo');
    if ($logo && file_exists(UPLOAD_DIR . '/' . $logo)) {
        $pdf->Image(UPLOAD_DIR . '/' . $logo, ($w - 20) / 2, 14, 20, 20);
        $pdf->SetY(36);
    } else {
        $pdf->SetY(24);
    }
    $pdf->SetTextColor(...$ink);
    $pdf->SetFont('DejaVu', 'B', 22);
    $pdf->Cell(0, 12, setting('site_name'), 0, 1, 'C');

    $pdf->SetTextColor(...$orange);
    $pdf->SetFont('DejaVu', 'B', 15);
    $pdf->Cell(0, 10, 'CERTIFICATE OF APPRECIATION', 0, 1, 'C');

    $pdf->SetTextColor(...$muted);
    $pdf->SetFont('DejaVu', '', 13);
    $pdf->Ln(6);
    $pdf->Cell(0, 8, 'This certifies that', 0, 1, 'C');

    $pdf->SetTextColor(11, 58, 114);
    $pdf->SetFont('DejaVu', 'B', 26);
    $pdf->Cell(0, 16, $donation['donor_name'], 0, 1, 'C');

    $pdf->SetTextColor(...$ink);
    $pdf->SetFont('DejaVu', '', 13);
    $pdf->SetX(30);
    $pdf->MultiCell($w - 60, 8, $message, 0, 'C', false);

    if (setting('pan_80g')) {
        $pdf->SetTextColor(...$muted);
        $pdf->SetFont('DejaVu', '', 10);
        $pdf->Ln(4);
        $pdf->Cell(0, 6, setting('pan_80g'), 0, 1, 'C');
    }

    // footer: QR verification + signature line
    $footerY = $h - 38;
    $qr = _donation_pdf_qr(BASE_URL . '/donate/certificate/' . $donation['cert_code']);
    if ($qr) {
        $pdf->Image($qr, 20, $footerY, 22, 22, 'PNG');
        @unlink($qr);
    }
    $pdf->SetXY(20, $footerY + 23);
    $pdf->SetFont('DejaVu', '', 8);
    $pdf->SetTextColor(...$muted);
    $pdf->Cell(30, 5, 'Cert. ' . $donation['cert_code'], 0, 0, 'L');

    $sigImage = setting('cert_signature_image');
    if ($sigImage && file_exists(UPLOAD_DIR . '/' . $sigImage)) {
        $pdf->Image(UPLOAD_DIR . '/' . $sigImage, $w - 82, $footerY - 6, 40, 14);
    }
    $pdf->SetXY($w - 90, $footerY + 10);
    $pdf->SetDrawColor(...$muted);
    $pdf->Line($w - 90, $footerY + 10, $w - 24, $footerY + 10);
    $pdf->SetXY($w - 90, $footerY + 12);
    $pdf->SetFont('DejaVu', 'B', 10);
    $pdf->SetTextColor(...$ink);
    $pdf->Cell(66, 6, 'Authorized Signatory', 0, 1, 'C');
    $pdf->SetX($w - 90);
    $pdf->SetFont('DejaVu', '', 9);
    $pdf->SetTextColor(...$muted);
    $pdf->Cell(66, 5, setting('cert_signatory_name') ?: setting('site_name'), 0, 1, 'C');
    if (setting('cert_signatory_designation')) {
        $pdf->SetX($w - 90);
        $pdf->Cell(66, 5, setting('cert_signatory_designation'), 0, 0, 'C');
    }

    return $pdf->Output('S');
}

/** Generates a formal donation payment receipt as raw PDF bytes. */
/**
 * Official donation receipt, formatted to the NGO's prescribed layout
 * (letterhead + donor details + donation details + 80G declaration +
 * signatory block). Branding (logo, signature, PAN, 80G URN, signatory)
 * is admin-editable under Settings and applies to every future receipt.
 */
function generate_donation_receipt_pdf(array $donation): string
{
    $pdf = _donation_pdf_new('P');
    $w = 210;
    $ink = [30, 40, 54]; $muted = [91, 107, 126]; $blue = [20, 84, 156];
    $margin = 18;

    $pdf->SetFillColor(...$blue);
    $pdf->Rect(0, 0, $w, 3, 'F');
    $pdf->SetDrawColor(...$blue);
    $pdf->SetLineWidth(0.4);
    $pdf->Rect(10, 8, $w - 20, 279 - 16);

    // ---- Letterhead ----
    $logo = setting('org_logo');
    if ($logo && file_exists(UPLOAD_DIR . '/' . $logo)) {
        $pdf->Image(UPLOAD_DIR . '/' . $logo, $margin + 2, 13, 22, 22);
    }
    $pdf->SetY(14);
    $pdf->SetTextColor(...$ink);
    $pdf->SetFont('DejaVu', 'B', 17);
    $pdf->Cell(0, 8, strtoupper(setting('site_name')), 0, 1, 'C');
    $pdf->SetFont('DejaVu', '', 10);
    $pdf->SetTextColor(...$muted);
    if (setting('site_tagline')) $pdf->Cell(0, 5, setting('site_tagline'), 0, 1, 'C');
    $pdf->SetFont('DejaVu', '', 8);
    $pdf->Cell(0, 4, '(' . (setting('org_legal_status') ?: 'Section 8 Company / Registered NGO') . ')', 0, 1, 'C');
    $pdf->Ln(1);
    if (setting('site_address')) $pdf->Cell(0, 4, 'Address: ' . setting('site_address'), 0, 1, 'C');
    $panLine = trim('PAN: ' . (setting('org_pan') ?: '-') . '   |   Registration No.: ' . (setting('registration_no') ?: '-'));
    $pdf->Cell(0, 4, $panLine, 0, 1, 'C');
    if (setting('org_80g_urn')) $pdf->Cell(0, 4, '80G Approval/URN: ' . setting('org_80g_urn'), 0, 1, 'C');
    $contactLine = trim('Mobile: ' . (setting('site_phone') ?: '-') . '   |   E-mail: ' . (setting('site_email') ?: '-'));
    $pdf->Cell(0, 4, $contactLine, 0, 1, 'C');

    $pdf->Ln(3);
    $pdf->SetDrawColor(...$muted);
    $pdf->Line($margin, $pdf->GetY(), $w - $margin, $pdf->GetY());
    $pdf->Ln(4);

    $pdf->SetFont('DejaVu', 'B', 15);
    $pdf->SetTextColor(...$blue);
    $pdf->Cell(0, 8, 'DONATION RECEIPT', 0, 1, 'C');
    $pdf->Ln(2);

    // Receipt No. / Date
    $pdf->SetFont('DejaVu', 'B', 10);
    $pdf->SetTextColor(...$ink);
    $pdf->SetX($margin);
    $pdf->Cell(95, 6, 'Receipt No.: ' . ($donation['receipt_no'] ?? ('DON-' . $donation['id'])), 0, 0, 'L');
    $pdf->Cell(0, 6, 'Date: ' . format_date($donation['created_at'], 'd/m/Y'), 0, 1, 'R');
    $pdf->Ln(3);

    $sectionHeader = function (string $title) use ($pdf, $margin, $w, $blue) {
        $pdf->SetX($margin);
        $pdf->SetFillColor(231, 240, 251);
        $pdf->SetTextColor(...$blue);
        $pdf->SetFont('DejaVu', 'B', 10);
        $pdf->Cell($w - 2 * $margin, 6, '  ' . $title, 0, 1, 'L', true);
        $pdf->Ln(1);
    };
    $row = function (string $label, string $value) use ($pdf, $margin, $ink) {
        $pdf->SetX($margin);
        $pdf->SetTextColor(...$ink);
        $pdf->SetFont('DejaVu', 'B', 9.5);
        $pdf->Cell(48, 6, $label, 0, 0, 'L');
        $pdf->SetFont('DejaVu', '', 9.5);
        $pdf->Cell(0, 6, $value !== '' ? $value : '-', 0, 1, 'L');
    };

    // ---- Donor details ----
    $sectionHeader('DONOR DETAILS');
    $row('Donor Name:', $donation['donor_name']);
    $row('Address:', $donation['address'] ?? '');
    $row('PAN:', $donation['pan'] ?? '');
    $row('Mobile No.:', $donation['phone'] ?? '');
    $row('E-mail:', $donation['email'] ?? '');
    $pdf->Ln(2);

    // ---- Donation details ----
    $sectionHeader('DONATION DETAILS');
    $row('Donation Amount:', format_inr($donation['amount']));
    $pdf->SetX($margin);
    $pdf->SetFont('DejaVu', 'B', 9.5);
    $pdf->Cell(48, 6, 'Amount in Words:', 0, 1, 'L');
    $pdf->SetX($margin);
    $pdf->SetFont('DejaVu', '', 9.5);
    $pdf->MultiCell($w - 2 * $margin, 5.5, amount_in_words((float) $donation['amount']), 0, 'L');
    $row('Date of Donation:', format_date($donation['created_at'], 'd/m/Y'));

    // Payment mode checkboxes
    $pdf->SetX($margin);
    $pdf->SetFont('DejaVu', 'B', 9.5);
    $pdf->Cell(48, 6, 'Payment Mode:', 0, 0, 'L');
    $modes = ['upi' => 'UPI', 'cheque' => 'Cheque', 'bank' => 'Bank Transfer', 'cash' => 'Cash'];
    $pdf->SetFont('DejaVu', '', 9.5);
    foreach ($modes as $val => $label) {
        $boxY = $pdf->GetY() + 1;
        $boxX = $pdf->GetX();
        $pdf->SetDrawColor(...$ink);
        $pdf->Rect($boxX, $boxY, 3.2, 3.2);
        if ($donation['method'] === $val) {
            $pdf->SetFont('DejaVu', 'B', 9.5);
            $pdf->Text($boxX + 0.4, $boxY + 3, 'X');
            $pdf->SetFont('DejaVu', '', 9.5);
        }
        $pdf->SetX($boxX + 4.5);
        $pdf->Cell(28, 6, $label, 0, 0, 'L');
    }
    $pdf->Ln(6);

    if (!empty($donation['txn_ref'])) $row('Transaction/UTR No.:', $donation['txn_ref']);
    if ($donation['method'] === 'cheque') {
        if (!empty($donation['cheque_no'])) $row('Cheque No.:', $donation['cheque_no']);
        if (!empty($donation['donor_bank_name'])) $row('Bank Name:', $donation['donor_bank_name']);
    }
    $row('Purpose of Donation:', $donation['campaign_title'] ?? 'General Fund');
    $pdf->Ln(2);

    // ---- Declaration ----
    $sectionHeader('DECLARATION');
    $pdf->SetX($margin);
    $pdf->SetFont('DejaVu', '', 9.5);
    $pdf->SetTextColor(...$ink);
    $pdf->MultiCell($w - 2 * $margin, 5.2,
        'Received with thanks from ' . $donation['donor_name'] . ' a donation of ' . format_inr($donation['amount'])
        . ' for the above-mentioned purpose.', 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX($margin);
    $pdf->SetFont('DejaVu', '', 8);
    $pdf->SetTextColor(...$muted);
    $pdf->MultiCell($w - 2 * $margin, 4.4,
        'This receipt acknowledges the donation received by ' . setting('site_name') . '. Eligibility of deduction under '
        . 'Section 80G of the Income-tax Act, 1961 shall be subject to the applicable provisions and conditions of the '
        . 'Income-tax Act and the information reported by the Foundation to the Income-tax Department.', 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX($margin);
    $pdf->MultiCell($w - 2 * $margin, 4.4,
        'Note: For claiming deduction under Section 80G, the donor should retain the prescribed donation certificate/Form '
        . '10BE issued by the Foundation as applicable.', 0, 'L');

    // ---- Signatory block ----
    $pdf->Ln(6);
    $pdf->SetX($margin);
    $pdf->SetDrawColor(...$muted);
    $pdf->Line($margin, $pdf->GetY(), $w - $margin, $pdf->GetY());
    $pdf->Ln(6);
    $pdf->SetX($margin);
    $pdf->SetFont('DejaVu', '', 9.5);
    $pdf->SetTextColor(...$ink);
    $pdf->Cell(0, 5, 'For ' . setting('site_name'), 0, 1, 'L');

    $sigImage = setting('cert_signature_image');
    if ($sigImage && file_exists(UPLOAD_DIR . '/' . $sigImage)) {
        $pdf->Image(UPLOAD_DIR . '/' . $sigImage, $w - $margin - 45, $pdf->GetY(), 40, 16);
    }
    $pdf->Ln(16);
    $pdf->SetX($margin);
    $pdf->Cell(0, 5, 'Authorized Signatory', 0, 1, 'L');
    if (setting('cert_signatory_name')) { $pdf->SetX($margin); $pdf->Cell(0, 5, 'Name: ' . setting('cert_signatory_name'), 0, 1, 'L'); }
    if (setting('cert_signatory_designation')) { $pdf->SetX($margin); $pdf->Cell(0, 5, 'Designation: ' . setting('cert_signatory_designation'), 0, 1, 'L'); }
    $pdf->SetX($margin);
    $pdf->SetTextColor(...$muted);
    $pdf->SetFont('DejaVu', '', 8);
    $pdf->Cell(0, 5, 'Official Seal / Stamp', 0, 1, 'L');

    return $pdf->Output('S');
}

/* ---------------- CSRF protection ---------------- */
function csrf_token(): string
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_field(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}

function csrf_verify(): bool
{
    $sent = $_POST[CSRF_TOKEN_NAME] ?? '';
    return !empty($sent) && hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $sent);
}

/** Call at the top of every POST handler. Stops the request if the token is bad. */
function require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(419);
        die('Security check failed (invalid CSRF token). Please go back and try again.');
    }
}

/* ---------------- Flash messages ---------------- */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function flash_render(): string
{
    $f = flash_get();
    if (!$f) return '';
    $map = ['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'];
    $cls = $map[$f['type']] ?? 'info';
    return '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">'
         . e($f['message'])
         . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

/* ---------------- Input helpers ---------------- */
function post(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function get_param(string $key, string $default = ''): string
{
    return trim((string) ($_GET[$key] ?? $default));
}

function valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function valid_phone(string $phone): bool
{
    return (bool) preg_match('/^[0-9+\-\s]{8,15}$/', $phone);
}

function valid_pincode(string $pincode): bool
{
    return (bool) preg_match('/^[0-9]{6}$/', $pincode);
}

/** value => label, used by both the apply form and the admin member list. */
function id_proof_types(): array
{
    return [
        'aadhaar'         => 'Aadhaar Card',
        'voter_id'        => 'Voter ID',
        'passport'        => 'Passport',
        'driving_licence' => 'Driving Licence',
        'pan_card'        => 'PAN Card',
    ];
}

function volunteer_availability_options(): array
{
    return ['Weekends', 'Weekdays', 'Only for Specific Event', 'Flexible'];
}

/* ---------------- Slugs ---------------- */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . time();
}

function unique_slug(string $table, string $text, int $ignoreId = 0): string
{
    $slug = slugify($text);
    $base = $slug;
    $i = 1;
    while (Database::value("SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id != ?", [$slug, $ignoreId]) > 0) {
        $slug = $base . '-' . (++$i);
    }
    return $slug;
}

/* ---------------- File uploads ---------------- */
/**
 * Handle an uploaded file safely.
 * @return string|null stored relative path (e.g. "gallery/abc123.jpg") or null when no file
 * @throws RuntimeException with a user-friendly message on failure
 */
function handle_upload(string $field, string $subdir, string $allowed = ALLOWED_IMAGE_TYPES): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('File is too large. Maximum size is ' . round(MAX_UPLOAD_SIZE / 1048576) . ' MB.');
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedList = array_map('trim', explode(',', strtolower($allowed)));
    if (!in_array($ext, $allowedList, true)) {
        throw new RuntimeException('File type .' . $ext . ' is not allowed. Allowed: ' . $allowed);
    }
    // MIME sanity check
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $okMimes = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    if (!in_array($mime, $okMimes, true)) {
        throw new RuntimeException('File content does not match an allowed type.');
    }
    $dir = UPLOAD_DIR . '/' . trim($subdir, '/');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '-' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Could not save the uploaded file. Check folder permissions.');
    }
    return trim($subdir, '/') . '/' . $name;
}

function delete_upload(?string $relPath): void
{
    if ($relPath && file_exists(UPLOAD_DIR . '/' . $relPath)) {
        @unlink(UPLOAD_DIR . '/' . $relPath);
    }
}

/* ---------------- Dynamic image captcha (first-party, GD, no third-party service) ---------------- */
const CAPTCHA_TTL          = 300; // seconds a generated code stays valid
const CAPTCHA_MAX_ATTEMPTS = 5;   // failed checks before a throttling delay kicks in

/** Generates a fresh code, stores it server-side (session) and returns it for rendering. Never expose this value to the client except as pixels. */
function captcha_generate_code(): string
{
    $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0/O/1/I/L - stays readable
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $charset[random_int(0, strlen($charset) - 1)];
    }
    $_SESSION['captcha_code']     = $code;
    $_SESSION['captcha_expires']  = time() + CAPTCHA_TTL;
    $_SESSION['captcha_attempts'] = 0;
    return $code;
}

/** Renders a brand-new captcha challenge as a PNG and streams it straight to the browser. */
function captcha_stream_image(): void
{
    $code = captcha_generate_code();

    $width = 170;
    $height = 60;
    $img = imagecreatetruecolor($width, $height);

    $bg   = imagecolorallocate($img, 241, 246, 252); // --mist
    $ink  = imagecolorallocate($img, 11, 58, 114);   // --blue-deep - strong contrast against the mist background
    // light tints of the brand palette for noise, so it distorts without competing with the text
    $noiseColors = [
        imagecolorallocate($img, 176, 202, 229), // blue tint
        imagecolorallocate($img, 249, 199, 168), // orange tint
        imagecolorallocate($img, 176, 219, 194), // green tint
    ];

    imagefilledrectangle($img, 0, 0, $width, $height, $bg);

    // a few soft wavy lines behind the text
    imagesetthickness($img, 1);
    for ($i = 0; $i < 3; $i++) {
        imageline(
            $img,
            random_int(0, $width), random_int(0, $height),
            random_int(0, $width), random_int(0, $height),
            $noiseColors[array_rand($noiseColors)]
        );
    }

    // light dot noise
    for ($i = 0; $i < 70; $i++) {
        imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $noiseColors[array_rand($noiseColors)]);
    }

    // characters on a readable baseline with only slight jitter (built-in bitmap font - no TTF/freetype dependency, works on any shared host)
    $font  = 5;
    $charW = imagefontwidth($font);
    $charH = imagefontheight($font);
    $baseY = (int) (($height - $charH) / 2);
    $x = (int) (($width - strlen($code) * ($charW + 10)) / 2);
    for ($i = 0; $i < strlen($code); $i++) {
        $y = $baseY + random_int(-4, 4);
        imagestring($img, $font, $x, $y, $code[$i], $ink);
        $x += $charW + random_int(9, 12);
    }

    header('Content-Type: image/png');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
    imagepng($img);
    imagedestroy($img);
}

/** Server-side only validation. The expected code never touches HTML, JS or the response body. */
function captcha_verify(): bool
{
    $attempts = (int) ($_SESSION['captcha_attempts'] ?? 0);
    if ($attempts >= CAPTCHA_MAX_ATTEMPTS) {
        sleep(2); // throttle brute-force guessing
    }

    $expired = empty($_SESSION['captcha_expires']) || time() > $_SESSION['captcha_expires'];
    $sent    = strtoupper(trim((string) post('captcha')));
    $ok      = !$expired && $sent !== '' && isset($_SESSION['captcha_code']) && hash_equals($_SESSION['captcha_code'], $sent);

    if (!$ok) {
        $_SESSION['captcha_attempts'] = $attempts + 1;
    }

    // single-use and non-replayable: gone the moment it's been checked, pass or fail
    unset($_SESSION['captcha_code'], $_SESSION['captcha_expires']);

    return $ok;
}

/* ---------------- Formatting ---------------- */
function format_date(?string $date, string $format = 'd M Y'): string
{
    if (empty($date) || $date === '0000-00-00') return '';
    return date($format, strtotime($date));
}

function format_inr($amount): string
{
    return '₹' . number_format((float) $amount, 0);
}

/** Indian numbering system (thousand/lakh/crore) word conversion, e.g. 125000 -> "One Lakh Twenty Five Thousand". */
function number_to_words_indian(int $num): string
{
    if ($num === 0) return 'Zero';
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
             'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $twoDigits = function (int $n) use ($ones, $tens): string {
        if ($n < 20) return $ones[$n];
        return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
    };
    $threeDigits = function (int $n) use ($twoDigits): string {
        $str = '';
        if ($n >= 100) {
            $str .= $twoDigits(intdiv($n, 100)) . ' Hundred ';
            $n %= 100;
        }
        return trim($str . $twoDigits($n));
    };

    $crore = intdiv($num, 10000000); $num %= 10000000;
    $lakh  = intdiv($num, 100000);   $num %= 100000;
    $thousand = intdiv($num, 1000);  $num %= 1000;
    $hundred = $num;

    $parts = [];
    if ($crore) $parts[] = $threeDigits($crore) . ' Crore';
    if ($lakh) $parts[] = $threeDigits($lakh) . ' Lakh';
    if ($thousand) $parts[] = $threeDigits($thousand) . ' Thousand';
    if ($hundred) $parts[] = $threeDigits($hundred);

    return implode(' ', $parts);
}

/** "Rupees One Lakh Twenty Five Thousand Only" - used on the donation receipt. */
function amount_in_words(float $amount): string
{
    $rupees = (int) floor($amount);
    $paise  = (int) round(($amount - $rupees) * 100);
    $words  = 'Rupees ' . number_to_words_indian($rupees);
    if ($paise > 0) {
        $words .= ' and ' . number_to_words_indian($paise) . ' Paise';
    }
    return $words . ' Only';
}

function excerpt(string $text, int $length = 140): string
{
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

/* ---------------- Pagination ---------------- */
function paginate(int $total, int $perPage, int $currentPage): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $pages));
    return [
        'total'   => $total,
        'pages'   => $pages,
        'current' => $currentPage,
        'offset'  => ($currentPage - 1) * $perPage,
        'limit'   => $perPage,
    ];
}

function pagination_links(array $p, string $baseUrl): string
{
    if ($p['pages'] <= 1) return '';
    $sep = (strpos($baseUrl, '?') !== false) ? '&' : '?';
    $html = '<nav aria-label="Pages"><ul class="pagination justify-content-center">';
    for ($i = 1; $i <= $p['pages']; $i++) {
        $active = $i === $p['current'] ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="'
               . e($baseUrl . $sep . 'pg=' . $i) . '">' . $i . '</a></li>';
    }
    return $html . '</ul></nav>';
}

/* ---------------- Location (state / district / taluka) ---------------- */
/** Active states for the initial dropdown. */
function location_states(): array
{
    return Database::all("SELECT id, name FROM states WHERE status='active' ORDER BY sort_order, name");
}

/** Active districts belonging to one state (used by both the AJAX endpoint and server-side rendering). */
function location_districts(int $stateId): array
{
    return Database::all(
        "SELECT id, name FROM districts WHERE state_id=? AND status='active' ORDER BY sort_order, name",
        [$stateId]
    );
}

/** Active talukas belonging to one district. */
function location_talukas(int $districtId): array
{
    return Database::all(
        "SELECT id, name FROM talukas WHERE district_id=? AND status='active' ORDER BY sort_order, name",
        [$districtId]
    );
}

/** Subcategories belonging to one job category (Careers filters/admin). */
function job_subcategories(int $categoryId): array
{
    return Database::all(
        "SELECT id, name FROM job_subcategories WHERE category_id=? ORDER BY sort_order, name",
        [$categoryId]
    );
}

/**
 * Server-side trust boundary: never accept a district/taluka id from a form
 * without confirming it actually belongs to the submitted parent. Prevents
 * a tampered POST (e.g. state_id=1 with a district_id from another state)
 * from being saved as a valid combination.
 */
function location_district_belongs_to_state(int $districtId, int $stateId): bool
{
    return (bool) Database::value(
        "SELECT COUNT(*) FROM districts WHERE id=? AND state_id=? AND status='active'",
        [$districtId, $stateId]
    );
}

function location_taluka_belongs_to_district(int $talukaId, int $districtId): bool
{
    return (bool) Database::value(
        "SELECT COUNT(*) FROM talukas WHERE id=? AND district_id=? AND status='active'",
        [$talukaId, $districtId]
    );
}

/* ---------------- IDs & codes ---------------- */
/**
 * Member ID format: {PREFIX}-{DISTRICT}-{YY}-{SEQ}, e.g. NGO-PUN-26-0001.
 * Prefix is configurable via the 'member_no_prefix' setting. District
 * segment uses the district's `code` when set, otherwise the first 3
 * letters of its name; omitted entirely if no district is known (keeps
 * the old MEM-YYYY-seq shape working for members with no location on file).
 */
function generate_member_no(?int $districtId = null): string
{
    $prefix = strtoupper(setting('member_no_prefix', 'MEM')) ?: 'MEM';
    $year   = date('y');

    $districtTag = '';
    if ($districtId) {
        $district = Database::one("SELECT name, code FROM districts WHERE id=?", [$districtId]);
        if ($district) {
            $raw = $district['code'] ?: preg_replace('/[^A-Za-z]/', '', $district['name']);
            $districtTag = strtoupper(substr($raw, 0, 3)) . '-';
        }
    }

    $like = "$prefix-$districtTag$year-%";
    $seq  = (int) Database::value("SELECT COUNT(*) FROM members WHERE member_no LIKE ?", [$like]) + 1;
    return sprintf('%s-%s%s-%04d', $prefix, $districtTag, $year, $seq);
}

function generate_cert_code(): string
{
    return strtoupper(bin2hex(random_bytes(4)));
}

function generate_receipt_no(): string
{
    return 'RCPT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}
