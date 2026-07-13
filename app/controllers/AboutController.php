<?php
class AboutController extends Controller
{
    public function index(): void
    {
        $this->render('about/index', [
            'pageTitle' => 'Who We Are',
            'section'   => Database::one("SELECT * FROM about_sections WHERE slug='who-we-are'"),
            'history'   => Database::one("SELECT * FROM about_sections WHERE slug='history'"),
        ]);
    }

    public function mission(): void
    {
        $this->render('about/mission', [
            'pageTitle' => 'Mission & Vision',
            'mission'   => Database::one("SELECT * FROM about_sections WHERE slug='mission'"),
            'vision'    => Database::one("SELECT * FROM about_sections WHERE slug='vision'"),
        ]);
    }

    public function board(): void
    {
        $this->render('about/people', [
            'pageTitle' => 'Board Members',
            'heading'   => 'Board Members',
            'people'    => Database::all("SELECT * FROM people WHERE type='board' AND is_active=1 ORDER BY sort_order"),
        ]);
    }

    public function team(): void
    {
        $this->render('about/people', [
            'pageTitle' => 'Our Team',
            'heading'   => 'Our Team',
            'people'    => Database::all("SELECT * FROM people WHERE type='team' AND is_active=1 ORDER BY sort_order"),
        ]);
    }

    public function achievements(): void
    {
        $this->render('about/achievements', [
            'pageTitle'    => 'Achievements & Awards',
            'achievements' => Database::all("SELECT * FROM achievements ORDER BY sort_order"),
        ]);
    }

    public function certificates(): void
    {
        $this->render('about/certificates', [
            'pageTitle'    => 'Certificates & Legal Information',
            'certificates' => Database::all("SELECT * FROM org_certificates ORDER BY sort_order"),
            'legal'        => Database::one("SELECT * FROM about_sections WHERE slug='legal'"),
        ]);
    }
}
