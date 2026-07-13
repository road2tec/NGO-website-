<?php
/**
 * Base controller - all public controllers extend this.
 */
abstract class Controller
{
    /**
     * Render a view inside the site layout.
     * $data keys become variables inside the view.
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';
        require dirname(__DIR__) . '/views/layouts/header.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<div class="container py-5"><div class="alert alert-warning">View not found: ' . e($view) . '</div></div>';
        }
        require dirname(__DIR__) . '/views/layouts/footer.php';
    }

    /** Render a bare view (no site header/footer) - used for printable ID cards & certificates */
    protected function renderBare(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . '/views/' . $view . '.php';
    }

    protected function notFound(string $message = 'Page not found'): void
    {
        http_response_code(404);
        $this->render('errors/404', ['pageTitle' => 'Not found', 'message' => $message]);
        exit;
    }
}
