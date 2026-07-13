<?php
/** Static legal pages: privacy, terms, refund, disclaimer */
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
        $this->render('pages/' . $slug, ['pageTitle' => $titles[$slug]]);
    }
}
