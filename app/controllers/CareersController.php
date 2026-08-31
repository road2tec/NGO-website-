<?php
class CareersController extends Controller
{
    public function index(): void
    {
        $categoryId    = (int) get_param('category_id');
        $subcategoryId = (int) get_param('subcategory_id');
        $location      = trim(get_param('location'));
        $employmentType = get_param('employment_type');
        $validTypes    = ['full_time', 'part_time', 'contract', 'internship', 'volunteer'];
        if (!in_array($employmentType, $validTypes, true)) $employmentType = '';

        $conditions = ['j.is_active = 1', "(j.deadline IS NULL OR j.deadline >= CURDATE())"];
        $params = [];
        if ($categoryId) { $conditions[] = 'j.category_id = ?'; $params[] = $categoryId; }
        if ($subcategoryId) { $conditions[] = 'j.subcategory_id = ?'; $params[] = $subcategoryId; }
        if ($location !== '') { $conditions[] = 'j.location LIKE ?'; $params[] = '%' . $location . '%'; }
        if ($employmentType) { $conditions[] = 'j.employment_type = ?'; $params[] = $employmentType; }
        $where = implode(' AND ', $conditions);

        $perPage = 9;
        $page = max(1, (int) get_param('pg', '1'));
        $total = (int) Database::value("SELECT COUNT(*) FROM jobs j WHERE $where", $params);
        $p = paginate($total, $perPage, $page);

        $jobs = Database::all(
            "SELECT j.*, c.name AS category_name, sc.name AS subcategory_name FROM jobs j
             LEFT JOIN job_categories c ON c.id = j.category_id
             LEFT JOIN job_subcategories sc ON sc.id = j.subcategory_id
             WHERE $where ORDER BY j.is_featured DESC, j.created_at DESC
             LIMIT {$p['limit']} OFFSET {$p['offset']}", $params
        );

        $this->render('careers/index', [
            'pageTitle'      => 'Careers',
            'jobs'           => $jobs,
            'pagination'     => $p,
            'categories'     => Database::all("SELECT * FROM job_categories ORDER BY sort_order, name"),
            'subcategories'  => $categoryId ? job_subcategories($categoryId) : [],
            'categoryId'     => $categoryId,
            'subcategoryId'  => $subcategoryId,
            'location'       => $location,
            'employmentType' => $employmentType,
        ]);
    }

    public function detail(?string $slug = null): void
    {
        $job = $slug ? Database::one(
            "SELECT j.*, c.name AS category_name, sc.name AS subcategory_name FROM jobs j
             LEFT JOIN job_categories c ON c.id = j.category_id
             LEFT JOIN job_subcategories sc ON sc.id = j.subcategory_id
             WHERE j.slug=? AND j.is_active=1", [$slug]
        ) : null;
        if (!$job) $this->notFound('This job opening was not found or is no longer active.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $errors = [];
            $fullName = post('full_name');
            if ($fullName === '') $errors[] = 'Full name is required.';
            if (!valid_email(post('email'))) $errors[] = 'A valid email is required.';
            if (!valid_phone(post('phone'))) $errors[] = 'A valid phone number is required.';
            if (!captcha_verify()) $errors[] = 'Captcha answer was wrong. Please try again.';

            $resumeFile = null;
            if (!empty($_FILES['resume']['name'])) {
                try {
                    $resumeFile = handle_upload('resume', 'private/resumes', ALLOWED_DOC_TYPES);
                } catch (RuntimeException $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if ($errors) {
                flash_set('error', implode(' ', $errors));
            } else {
                Database::insert('job_applications', [
                    'job_id'       => $job['id'],
                    'full_name'    => $fullName,
                    'email'        => post('email'),
                    'phone'        => post('phone'),
                    'location'     => post('location'),
                    'education'    => post('education'),
                    'experience'   => post('experience'),
                    'skills'       => post('skills'),
                    'cover_letter' => post('cover_letter'),
                    'resume_file'  => $resumeFile,
                ]);
                send_mail(
                    post('email'),
                    'Application received - ' . $job['title'] . ' at ' . setting('site_name'),
                    "Dear $fullName,\n\nThank you for applying for \"" . $job['title'] . "\" at " . setting('site_name') . ".\n\n"
                    . "We've received your application and our team will review it. If shortlisted, we'll contact you at this email or your phone number.\n\n"
                    . "With thanks,\n" . setting('site_name'),
                    $fullName
                );
                flash_set('success', 'Your application has been submitted. Thank you for your interest - we will contact you if shortlisted.');
                redirect('careers/detail/' . $job['slug']);
            }
        }

        $this->render('careers/detail', [
            'pageTitle' => $job['title'],
            'metaDesc'  => excerpt($job['description'] ?? '', 150),
            'job'       => $job,
        ]);
    }
}
