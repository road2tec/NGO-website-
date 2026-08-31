<?php
class MembershipController extends Controller
{
    public function index(): void
    {
        $this->render('membership/index', [
            'pageTitle'  => 'Membership',
            'categories' => Database::all("SELECT * FROM membership_categories ORDER BY fee"),
        ]);
    }

    public function benefits(): void
    {
        $this->index();
    }

    public function apply(): void
    {
        $categories = Database::all("SELECT * FROM membership_categories ORDER BY fee");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $errors = [];

            $firstName = post('first_name');
            $middleName = post('middle_name');
            $surname = post('surname');
            if ($firstName === '')                   $errors[] = 'First name is required.';
            if ($surname === '')                      $errors[] = 'Surname is required.';
            if (!in_array(post('gender'), ['Male', 'Female', 'Other'], true)) $errors[] = 'Gender is required.';
            $dob = post('dob');
            if ($dob === '')                          $errors[] = 'Date of birth is required.';
            elseif (!$this->validDob($dob))            $errors[] = 'Enter a valid date of birth (not in the future).';
            if (!valid_email(post('email')))          $errors[] = 'A valid email is required.';
            if (!valid_phone(post('phone')))          $errors[] = 'A valid phone number is required.';
            if (post('address') === '')                $errors[] = 'Address is required.';
            if (!valid_pincode(post('pincode')))       $errors[] = 'Pincode must be exactly 6 digits.';
            if (strlen(post('password')) < 6)         $errors[] = 'Password must be at least 6 characters.';
            if (post('password') !== post('password2')) $errors[] = 'Passwords do not match.';
            if (!captcha_verify())                    $errors[] = 'Captcha answer was wrong.';
            if (Database::value("SELECT COUNT(*) FROM members WHERE email=?", [post('email')]) > 0) {
                $errors[] = 'This email is already registered. Use "Check Membership Status" instead.';
            }

            // Location: state is always a real record; district/taluka may be 'other'.
            $stateId = (int) post('state_id');
            $state = $stateId ? Database::one("SELECT id FROM states WHERE id=? AND status='active'", [$stateId]) : null;
            if (!$state) $errors[] = 'Please select a valid state.';

            $districtId = null; $districtOther = null;
            $districtRaw = post('district_id');
            if ($districtRaw === 'other') {
                $districtOther = post('district_other');
                if ($districtOther === '') $errors[] = 'Please enter your district (Other).';
            } elseif ($state && ctype_digit($districtRaw) && location_district_belongs_to_state((int) $districtRaw, $stateId)) {
                $districtId = (int) $districtRaw;
            } else {
                $errors[] = 'Please select a valid district.';
            }

            // No validated district means there's no taluka list to check against
            // either - the taluka is necessarily free text in that case too.
            $talukaId = null; $talukaOther = null;
            $talukaRaw = post('taluka_id');
            if ($districtId === null) {
                $talukaOther = post('taluka_other');
                if ($talukaOther === '') $errors[] = 'Please enter your taluka (Other).';
            } elseif ($talukaRaw === 'other') {
                $talukaOther = post('taluka_other');
                if ($talukaOther === '') $errors[] = 'Please enter your taluka (Other).';
            } elseif (ctype_digit($talukaRaw) && location_taluka_belongs_to_district((int) $talukaRaw, $districtId)) {
                $talukaId = (int) $talukaRaw;
            } else {
                $errors[] = 'Please select a valid taluka.';
            }

            $idProofType = post('id_proof_type');
            if (!array_key_exists($idProofType, id_proof_types())) $errors[] = 'Please select an ID proof type.';
            if (post('id_proof_number') === '')       $errors[] = 'ID proof document number is required.';
            if (empty($_FILES['id_proof_file']['name'])) $errors[] = 'Please upload your ID proof document.';

            if (!post('terms'))                       $errors[] = 'You must agree to the Terms & Conditions and Privacy Policy.';

            if ($errors) {
                flash_set('error', implode(' ', $errors));
            } else {
                try {
                    $photo = handle_upload('photo', 'members');
                    $idProofFile = handle_upload('id_proof_file', 'private/id_proofs', ALLOWED_DOC_TYPES);
                    $fullName = trim($firstName . ' ' . ($middleName !== '' ? $middleName . ' ' : '') . $surname);
                    Database::insert('members', [
                        'category_id'      => (int) post('category_id') ?: null,
                        'name'             => $fullName,
                        'first_name'       => $firstName,
                        'middle_name'      => $middleName ?: null,
                        'surname'          => $surname,
                        'photo'            => $photo,
                        'dob'              => $dob,
                        'gender'           => post('gender'),
                        'email'            => post('email'),
                        'phone'            => post('phone'),
                        'address'          => post('address'),
                        'occupation'       => post('occupation'),
                        'blood_group'      => post('blood_group'),
                        'state_id'         => $stateId,
                        'district_id'      => $districtId,
                        'district_other'   => $districtOther,
                        'taluka_id'        => $talukaId,
                        'taluka_other'     => $talukaOther,
                        'pincode'          => post('pincode'),
                        'id_proof_type'    => $idProofType,
                        'id_proof_number'  => post('id_proof_number'),
                        'id_proof_file'    => $idProofFile,
                        'terms_accepted_at' => date('Y-m-d H:i:s'),
                        'password'         => password_hash(post('password'), PASSWORD_DEFAULT),
                    ]);
                    flash_set('success', 'Application received! Your membership is pending admin approval. You can check the status anytime with your email and password.');
                    redirect('membership/status');
                } catch (RuntimeException $e) {
                    flash_set('error', $e->getMessage());
                }
            }
            redirect('membership/apply');
        }

        $this->render('membership/apply', [
            'pageTitle'   => 'Apply for Membership',
            'categories'  => $categories,
            'states'      => location_states(),
            'idProofTypes' => id_proof_types(),
        ]);
    }

    private function validDob(string $dob): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$d || $d->format('Y-m-d') !== $dob) return false;
        $now = new DateTime();
        return $d <= $now && $d->format('Y') >= 1900;
    }

    /** Public member directory (approved members only, limited fields) */
    public function members(): void
    {
        $q  = get_param('q');
        $pg = max(1, (int) get_param('pg', '1'));
        $where  = "status='approved'";
        $params = [];
        if ($q !== '') {
            $where .= " AND (name LIKE ? OR member_no LIKE ?)";
            $params = ["%$q%", "%$q%"];
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM members WHERE $where", $params);
        $p = paginate($total, 24, $pg);
        $rows = Database::all(
            "SELECT member_no, name, photo, occupation, created_at FROM members
             WHERE $where ORDER BY name LIMIT {$p['limit']} OFFSET {$p['offset']}", $params);

        $this->render('membership/members', [
            'pageTitle' => 'Our Members', 'members' => $rows, 'q' => $q, 'p' => $p,
        ]);
    }

    /** Member login for status / dashboard / ID card */
    public function login(): void
    {
        if (!empty($_SESSION['member_id'])) redirect('membership/dashboard');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $member = Database::one("SELECT * FROM members WHERE email=?", [post('email')]);
            if ($member && password_verify(post('password'), $member['password'])) {
                session_regenerate_id(true);
                $_SESSION['member_id'] = (int) $member['id'];
                redirect('membership/dashboard');
            }
            flash_set('error', 'Email or password is incorrect.');
            redirect('membership/login');
        }
        $this->render('membership/login', ['pageTitle' => 'Member Login']);
    }

    public function logout(): void
    {
        unset($_SESSION['member_id']);
        flash_set('info', 'You have been logged out.');
        redirect('membership/login');
    }

    /** Request a password-reset email. Never reveals whether the email is registered. */
    public function forgot(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            if (!captcha_verify()) {
                flash_set('error', 'Captcha answer was wrong.');
                redirect('membership/forgot');
            }
            $email = post('email');
            $member = valid_email($email) ? Database::one("SELECT id FROM members WHERE email=?", [$email]) : null;
            if ($member) {
                // simple rate limit: at most one new token every 2 minutes per member
                $recent = (int) Database::value(
                    "SELECT COUNT(*) FROM password_reset_tokens WHERE member_id=? AND created_at > (NOW() - INTERVAL 2 MINUTE)",
                    [$member['id']]
                );
                if ($recent === 0) {
                    Database::update('password_reset_tokens', ['used_at' => date('Y-m-d H:i:s')], 'member_id=? AND used_at IS NULL', [$member['id']]);
                    $rawToken = bin2hex(random_bytes(32));
                    Database::insert('password_reset_tokens', [
                        'member_id'  => $member['id'],
                        'token_hash' => hash('sha256', $rawToken),
                        'expires_at' => date('Y-m-d H:i:s', time() + 3600),
                    ]);
                    $resetLink = url('membership/reset/' . $rawToken);
                    send_mail($email, 'Reset your password - ' . setting('site_name'),
                        "Hello,\n\nWe received a request to reset your membership account password.\n\n"
                        . "Click the link below to set a new password. This link is valid for 1 hour and can only be used once.\n\n"
                        . "$resetLink\n\nIf you did not request this, you can safely ignore this email.\n\n"
                        . setting('site_name'));
                }
            }
            flash_set('success', 'If that email is registered, a password reset link has been sent. Please check your inbox.');
            redirect('membership/login');
        }
        $this->render('membership/forgot', [
            'pageTitle' => 'Forgot Password',
        ]);
    }

    /** Reset via a single-use, expiring token from the forgot-password email. */
    public function reset(?string $token = null): void
    {
        if (!$token) $this->notFound('Invalid password reset link.');
        $row = Database::one(
            "SELECT * FROM password_reset_tokens WHERE token_hash=? AND used_at IS NULL AND expires_at > NOW()",
            [hash('sha256', $token)]
        );
        if (!$row) {
            flash_set('error', 'This password reset link is invalid or has expired. Please request a new one.');
            redirect('membership/forgot');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            if (strlen(post('password')) < 6) {
                flash_set('error', 'Password must be at least 6 characters.');
            } elseif (post('password') !== post('password2')) {
                flash_set('error', 'Passwords do not match.');
            } else {
                Database::update('members', ['password' => password_hash(post('password'), PASSWORD_DEFAULT)], 'id=?', [$row['member_id']]);
                Database::update('password_reset_tokens', ['used_at' => date('Y-m-d H:i:s')], 'id=?', [$row['id']]);
                flash_set('success', 'Your password has been reset. Please log in with your new password.');
                redirect('membership/login');
            }
            redirect('membership/reset/' . $token);
        }

        $this->render('membership/reset', ['pageTitle' => 'Reset Password', 'token' => $token]);
    }

    public function status(): void
    {
        $this->login();
    }

    public function dashboard(): void
    {
        $member = $this->requireMember();
        $this->render('membership/dashboard', [
            'pageTitle'    => 'Member Dashboard',
            'member'       => $member,
            'category'     => $member['category_id']
                ? Database::one("SELECT * FROM membership_categories WHERE id=?", [$member['category_id']])
                : null,
            'unreadCount'  => (int) Database::value("SELECT COUNT(*) FROM member_notifications WHERE member_id=? AND is_read=0", [$member['id']]),
        ]);
    }

    /** Notification inbox; opening it marks everything as read. */
    public function notifications(): void
    {
        $member = $this->requireMember();
        $notifications = Database::all(
            "SELECT * FROM member_notifications WHERE member_id=? ORDER BY created_at DESC", [$member['id']]
        );
        Database::update('member_notifications', ['is_read' => 1], 'member_id=? AND is_read=0', [$member['id']]);
        $this->render('membership/notifications', [
            'pageTitle'     => 'Notifications',
            'notifications' => $notifications,
        ]);
    }

    /** Printable ID card with QR code (approved members only) */
    public function idcard(): void
    {
        $member = $this->requireMember();
        if ($member['status'] !== 'approved') {
            flash_set('info', 'Your ID card will be available once the admin approves your membership. Current status: ' . $member['status'] . '.');
            redirect('membership/dashboard');
        }
        $category = $member['category_id']
            ? Database::one("SELECT name FROM membership_categories WHERE id=?", [$member['category_id']])
            : null;
        $mission = Database::one("SELECT content FROM about_sections WHERE slug='mission'");
        $this->renderBare('membership/idcard', ['member' => $member, 'category' => $category, 'mission' => $mission]);
    }

    private function requireMember(): array
    {
        if (empty($_SESSION['member_id'])) {
            flash_set('info', 'Please log in with the email and password you used while applying.');
            redirect('membership/login');
        }
        $member = Database::one("SELECT * FROM members WHERE id=?", [$_SESSION['member_id']]);
        if (!$member) {
            unset($_SESSION['member_id']);
            redirect('membership/login');
        }
        return $member;
    }
}
