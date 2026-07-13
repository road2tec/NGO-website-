<?php
class ProjectsController extends Controller
{
    public function index(): void
    {
        $this->type('ongoing');
    }

    public function type(?string $type = 'ongoing'): void
    {
        $valid = ['ongoing', 'completed', 'upcoming', 'government', 'csr'];
        $type  = in_array($type, $valid, true) ? $type : 'ongoing';
        $labels = [
            'ongoing' => 'Ongoing Projects', 'completed' => 'Completed Projects',
            'upcoming' => 'Upcoming Projects', 'government' => 'Government Projects', 'csr' => 'CSR Projects',
        ];
        $this->render('projects/index', [
            'pageTitle' => $labels[$type],
            'type'      => $type,
            'labels'    => $labels,
            'projects'  => Database::all("SELECT * FROM projects WHERE type=? ORDER BY is_featured DESC, id DESC", [$type]),
        ]);
    }

    public function detail(?string $slug = null): void
    {
        $project = $slug ? Database::one("SELECT * FROM projects WHERE slug=?", [$slug]) : null;
        if (!$project) $this->notFound('Project not found.');
        $this->render('projects/detail', [
            'pageTitle' => $project['title'],
            'metaDesc'  => $project['summary'] ?? '',
            'project'   => $project,
            'images'    => Database::all("SELECT * FROM project_images WHERE project_id=?", [$project['id']]),
        ]);
    }
}
