<?php
class ContactController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            if (!captcha_verify()) {
                flash_set('error', 'Captcha answer was wrong. Please try again.');
            } elseif (post('name') === '' || !valid_email(post('email')) || post('message') === '') {
                flash_set('error', 'Please fill your name, a valid email and a message.');
            } else {
                Database::insert('contact_messages', [
                    'name' => post('name'), 'email' => post('email'), 'phone' => post('phone'),
                    'subject' => post('subject'), 'message' => post('message'), 'source' => 'contact',
                ]);
                // The message is already saved above regardless of whether this notification email goes through.
                send_mail(setting('site_email'), 'Website contact: ' . post('subject'),
                      post('message') . "\n\nFrom: " . post('name') . ' <' . post('email') . '> ' . post('phone'));
                flash_set('success', 'Message sent! We usually reply within 2 working days.');
            }
            redirect('contact');
        }

        $this->render('contact/index', [
            'pageTitle' => 'Contact Us',
        ]);
    }
}
