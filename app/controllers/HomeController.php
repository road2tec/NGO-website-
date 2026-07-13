<?php
class HomeController extends Controller
{
    public function index(): void
    {
        // Homepage enquiry form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('form') === 'enquiry') {
            require_csrf();
            if (!captcha_verify()) {
                flash_set('error', 'Captcha answer was wrong. Please try again.');
            } elseif (post('name') === '' || !valid_email(post('email')) || post('message') === '') {
                flash_set('error', 'Please fill your name, a valid email and a message.');
            } else {
                Database::insert('contact_messages', [
                    'name' => post('name'), 'email' => post('email'),
                    'phone' => post('phone'), 'message' => post('message'),
                    'source' => 'homepage',
                ]);
                flash_set('success', 'Thank you! Your enquiry has been received. We will reply soon.');
            }
            redirect('');
        }

        $this->render('home/index', [
            'banners'      => Database::all("SELECT * FROM banners WHERE is_active=1 ORDER BY sort_order"),
            'about'        => Database::one("SELECT * FROM about_sections WHERE slug='who-we-are'"),
            'mission'      => Database::one("SELECT * FROM about_sections WHERE slug='mission'"),
            'vision'       => Database::one("SELECT * FROM about_sections WHERE slug='vision'"),
            'achievements' => Database::all("SELECT * FROM achievements ORDER BY sort_order LIMIT 3"),
            'projects'     => Database::all("SELECT * FROM projects WHERE is_featured=1 ORDER BY id DESC LIMIT 3"),
            'activities'   => Database::all("SELECT * FROM events WHERE event_date <= CURDATE() ORDER BY event_date DESC LIMIT 3"),
            'upcoming'     => Database::all("SELECT * FROM events WHERE event_date > CURDATE() ORDER BY event_date ASC LIMIT 3"),
            'galleryItems' => Database::all("SELECT * FROM gallery_items WHERE type='image' ORDER BY id DESC LIMIT 6"),
            'testimonials' => Database::all("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order"),
            'news'         => Database::all("SELECT * FROM news WHERE is_published=1 ORDER BY published_at DESC LIMIT 3"),
            'campaign'     => Database::one("SELECT * FROM campaigns WHERE is_active=1 ORDER BY id DESC LIMIT 1"),
            'sponsors'     => Database::all("SELECT * FROM sponsors ORDER BY sort_order"),
        ]);
    }

    public function newsletter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $email = post('email');
            if (valid_email($email)) {
                try {
                    Database::insert('newsletter_subscribers', ['email' => $email]);
                    flash_set('success', 'Subscribed! You will receive our updates.');
                } catch (Throwable $e) {
                    flash_set('info', 'This email is already subscribed.');
                }
            } else {
                flash_set('error', 'Please enter a valid email address.');
            }
        }
        redirect('');
    }
}
