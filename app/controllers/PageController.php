<?php
/** Legal pages: privacy, terms, refund, disclaimer. Content lives in about_sections (Admin -> About Sections), same table the About Us page already uses. */
class PageController extends Controller
{
    public function index(?string $slug = null): void
    {
        $pages = ['privacy', 'terms', 'refund', 'disclaimer'];
        if (!in_array($slug, $pages, true)) $this->notFound();
        $titles = [
            'privacy' => 'Privacy Policy', 'terms' => 'Terms & Conditions',
            'refund' => 'Refund Policy', 'disclaimer' => 'Disclaimer',
        ];
        $this->render('pages/legal', [
            'pageTitle' => $titles[$slug],
            'section'   => Database::one("SELECT * FROM about_sections WHERE slug=?", [$slug]),
        ]);
    }
}
