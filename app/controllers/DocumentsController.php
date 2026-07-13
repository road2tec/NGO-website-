<?php
class DocumentsController extends Controller
{
    public function index(): void
    {
        $q = get_param('q');
        $cat = get_param('cat');
        $where = "is_visible=1";
        $params = [];
        if ($q !== '')   { $where .= " AND title LIKE ?"; $params[] = "%$q%"; }
        if ($cat !== '') { $where .= " AND category = ?"; $params[] = $cat; }

        $this->render('documents/index', [
            'pageTitle'  => 'Documents',
            'documents'  => Database::all("SELECT * FROM documents WHERE $where ORDER BY category, title", $params),
            'categories' => array_column(Database::all("SELECT DISTINCT category FROM documents WHERE is_visible=1 ORDER BY category"), 'category'),
            'q' => $q, 'cat' => $cat,
        ]);
    }

    /** Counted download */
    public function download(?string $id = null): void
    {
        $doc = Database::one("SELECT * FROM documents WHERE id=? AND is_visible=1", [(int) $id]);
        if (!$doc || empty($doc['file']) || !file_exists(UPLOAD_DIR . '/' . $doc['file'])) {
            flash_set('error', 'That document is not available for download yet.');
            redirect('documents');
        }
        Database::query("UPDATE documents SET downloads = downloads + 1 WHERE id=?", [$doc['id']]);
        $path = UPLOAD_DIR . '/' . $doc['file'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($doc['file']) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
