<?php
class NewsController extends Controller
{
    public function index(): void
    {
        $q = get_param('q');
        $pg = max(1, (int) get_param('pg', '1'));
        $where = "is_published=1";
        $params = [];
        if ($q !== '') { $where .= " AND (title LIKE ? OR excerpt LIKE ?)"; $params = ["%$q%", "%$q%"]; }
        $total = (int) Database::value("SELECT COUNT(*) FROM news WHERE $where", $params);
        $p = paginate($total, 9, $pg);

        $this->render('news/index', [
            'pageTitle' => 'News & Updates',
            'featured'  => Database::one("SELECT * FROM news WHERE is_published=1 AND is_featured=1 ORDER BY published_at DESC LIMIT 1"),
            'items'     => Database::all("SELECT * FROM news WHERE $where ORDER BY published_at DESC LIMIT {$p['limit']} OFFSET {$p['offset']}", $params),
            'q' => $q, 'p' => $p,
        ]);
    }

    public function detail(?string $slug = null): void
    {
        $item = $slug ? Database::one("SELECT * FROM news WHERE slug=? AND is_published=1", [$slug]) : null;
        if (!$item) $this->notFound('Article not found.');
        $this->render('news/detail', [
            'pageTitle' => $item['title'],
            'metaDesc'  => $item['excerpt'] ?? '',
            'item'      => $item,
            'more'      => Database::all("SELECT title, slug, published_at FROM news WHERE is_published=1 AND id != ? ORDER BY published_at DESC LIMIT 4", [$item['id']]),
        ]);
    }
}
