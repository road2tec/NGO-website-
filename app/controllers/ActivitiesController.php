<?php
class ActivitiesController extends Controller
{
    public function index(): void
    {
        $this->render('activities/index', [
            'pageTitle' => 'Activities & Events',
            'heading'   => 'All Activities',
            'upcoming'  => Database::all("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC"),
            'recent'    => Database::all("SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC LIMIT 12"),
        ]);
    }

    public function recent(): void
    {
        $this->render('activities/list', [
            'pageTitle' => 'Recent Activities',
            'heading'   => 'Recent Activities',
            'events'    => Database::all("SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC"),
        ]);
    }

    public function upcoming(): void
    {
        $this->render('activities/list', [
            'pageTitle' => 'Upcoming Events',
            'heading'   => 'Upcoming Events',
            'events'    => Database::all("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC"),
        ]);
    }

    public function detail(?string $slug = null): void
    {
        $event = $slug ? Database::one("SELECT * FROM events WHERE slug=?", [$slug]) : null;
        if (!$event) $this->notFound('Event not found.');

        // Event registration
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            if (!captcha_verify()) {
                flash_set('error', 'Captcha answer was wrong. Please try again.');
            } elseif (post('name') === '' || !valid_email(post('email')) || !valid_phone(post('phone'))) {
                flash_set('error', 'Please provide your name, a valid email and phone number.');
            } elseif (!$event['registration_open'] || $event['event_date'] < date('Y-m-d')) {
                flash_set('error', 'Registration for this event is closed.');
            } else {
                try {
                    Database::insert('event_registrations', [
                        'event_id' => $event['id'], 'name' => post('name'),
                        'email' => post('email'), 'phone' => post('phone'),
                    ]);
                    flash_set('success', 'Registered! Please note the Event ID ' . $event['event_code'] . ' — you will need it to download your participation certificate after the event.');
                } catch (Throwable $e) {
                    flash_set('info', 'This email is already registered for this event.');
                }
            }
            redirect('activities/detail/' . $event['slug']);
        }

        $this->render('activities/detail', [
            'pageTitle' => $event['title'],
            'event'     => $event,
            'regCount'  => (int) Database::value("SELECT COUNT(*) FROM event_registrations WHERE event_id=?", [$event['id']]),
        ]);
    }
}
