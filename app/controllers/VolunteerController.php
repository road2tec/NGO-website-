<?php
class VolunteerController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            if (!captcha_verify()) {
                flash_set('error', 'Captcha answer was wrong.');
            } elseif (post('name') === '' || !valid_email(post('email')) || !valid_phone(post('phone'))) {
                flash_set('error', 'Please fill your name, a valid email and phone.');
            } else {
                try {
                    $resume = handle_upload('resume', 'documents', ALLOWED_DOC_TYPES);
                    Database::insert('volunteers', [
                        'name' => post('name'), 'email' => post('email'), 'phone' => post('phone'),
                        'city' => post('city'), 'resume' => $resume,
                        'experience' => post('experience'), 'availability' => post('availability'),
                    ]);
                    flash_set('success', 'Thank you for offering your time! Our team will contact you after reviewing your application.');
                } catch (RuntimeException $e) {
                    flash_set('error', $e->getMessage());
                }
            }
            redirect('volunteer');
        }

        $this->render('volunteer/index', [
            'pageTitle' => 'Volunteer With Us',
            'captcha'   => captcha_question(),
        ]);
    }
}
