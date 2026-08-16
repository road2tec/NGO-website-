<?php
class VolunteerController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $firstName = post('first_name');
            $middleName = post('middle_name');
            $surname = post('surname');
            $errors = [];
            if ($firstName === '')                              $errors[] = 'First name is required.';
            if ($surname === '')                                 $errors[] = 'Surname is required.';
            if (!valid_email(post('email')))                    $errors[] = 'A valid email is required.';
            if (!valid_phone(post('phone')))                    $errors[] = 'A valid phone number is required.';
            if (!in_array(post('availability'), volunteer_availability_options(), true)) $errors[] = 'Please select your availability.';
            if (!post('consent'))                               $errors[] = 'You must agree to the volunteer consent statement.';
            if (!captcha_verify())                              $errors[] = 'Captcha answer was wrong.';

            if ($errors) {
                flash_set('error', implode(' ', $errors));
            } else {
                try {
                    $resume = handle_upload('resume', 'documents', ALLOWED_DOC_TYPES);
                    $fullName = trim($firstName . ' ' . ($middleName !== '' ? $middleName . ' ' : '') . $surname);
                    Database::insert('volunteers', [
                        'name' => $fullName, 'first_name' => $firstName, 'middle_name' => $middleName ?: null, 'surname' => $surname,
                        'email' => post('email'), 'phone' => post('phone'),
                        'city' => post('city'), 'resume' => $resume,
                        'experience' => post('experience'), 'availability' => post('availability'),
                        'consent_accepted_at' => date('Y-m-d H:i:s'),
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
