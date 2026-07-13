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
            if (post('name') === '')                 $errors[] = 'Name is required.';
            if (!valid_email(post('email')))         $errors[] = 'A valid email is required.';
            if (!valid_phone(post('phone')))         $errors[] = 'A valid phone number is required.';
            if (strlen(post('password')) < 6)        $errors[] = 'Password must be at least 6 characters.';
            if (post('password') !== post('password2')) $errors[] = 'Passwords do not match.';
            if (!captcha_verify())                   $errors[] = 'Captcha answer was wrong.';
            if (Database::value("SELECT COUNT(*) FROM members WHERE email=?", [post('email')]) > 0) {
                $errors[] = 'This email is already registered. Use "Check Membership Status" instead.';
            }

            if ($errors) {
                flash_set('error', implode(' ', $errors));
            } else {
                try {
                    $photo = handle_upload('photo', 'members');
                    Database::insert('members', [
                        'category_id' => (int) post('category_id') ?: null,
                        'name'        => post('name'),
                        'photo'       => $photo,
                        'dob'         => post('dob') ?: null,
                        'gender'      => in_array(post('gender'), ['Male','Female','Other']) ? post('gender') : null,
                        'email'       => post('email'),
                        'phone'       => post('phone'),
                        'address'     => post('address'),
                        'occupation'  => post('occupation'),
                        'blood_group' => post('blood_group'),
                        'aadhar'      => post('aadhar'),
                        'password'    => password_hash(post('password'), PASSWORD_DEFAULT),
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
            'pageTitle'  => 'Apply for Membership',
            'categories' => $categories,
            'captcha'    => captcha_question(),
        ]);
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

    public function status(): void
    {
        $this->login();
    }

    public function dashboard(): void
    {
        $member = $this->requireMember();
        $this->render('membership/dashboard', [
            'pageTitle' => 'Member Dashboard',
            'member'    => $member,
            'category'  => $member['category_id']
                ? Database::one("SELECT * FROM membership_categories WHERE id=?", [$member['category_id']])
                : null,
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
        $this->renderBare('membership/idcard', ['member' => $member]);
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
