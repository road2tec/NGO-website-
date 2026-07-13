<?php
/**
 * Event Participation Certificate:
 * user enters name/email + event ID -> printable certificate with QR verification.
 */
class CertificateController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $event = Database::one("SELECT * FROM events WHERE event_code=?", [post('event_code')]);
            $reg = $event ? Database::one(
                "SELECT * FROM event_registrations WHERE event_id=? AND email=? AND name LIKE ?",
                [$event['id'], post('email'), '%' . post('name') . '%']
            ) : null;

            if (!$reg) {
                flash_set('error', 'No matching registration found. Check the name, email and Event ID exactly as used during registration.');
            } elseif (!$reg['attended']) {
                flash_set('info', 'Your attendance for this event has not been marked yet. Certificates are released after the organisers confirm attendance.');
            } else {
                if (empty($reg['cert_code'])) {
                    $code = generate_cert_code();
                    Database::update('event_registrations',
                        ['cert_code' => $code, 'cert_issued_at' => date('Y-m-d H:i:s')],
                        'id=?', [$reg['id']]);
                    $reg['cert_code'] = $code;
                }
                redirect('certificate/view/' . $reg['cert_code']);
            }
            redirect('certificate');
        }

        $this->render('certificate/index', [
            'pageTitle' => 'Download Event Participation Certificate',
            'events'    => Database::all("SELECT title, event_code, event_date FROM events WHERE event_date <= CURDATE() ORDER BY event_date DESC LIMIT 10"),
        ]);
    }

    /** Printable certificate page */
    public function view(?string $code = null): void
    {
        $reg = $this->findByCode($code);
        if (!$reg) $this->notFound('Certificate not found.');
        $this->renderBare('certificate/view', ['reg' => $reg]);
    }

    /** Public QR verification endpoint */
    public function verify(?string $code = null): void
    {
        $reg = $this->findByCode($code);
        $this->render('certificate/verify', [
            'pageTitle' => 'Verify Certificate',
            'reg'       => $reg,
            'code'      => $code,
        ]);
    }

    private function findByCode(?string $code): ?array
    {
        if (!$code) return null;
        return Database::one(
            "SELECT er.*, e.title AS event_title, e.event_date, e.event_code, e.venue
             FROM event_registrations er JOIN events e ON e.id = er.event_id
             WHERE er.cert_code = ?", [$code]);
    }
}
