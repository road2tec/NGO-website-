<?php
$categoryOptions = ['' => 'Select a category'];
foreach (Database::all("SELECT id, name FROM job_categories ORDER BY sort_order, name") as $c) {
    $categoryOptions[$c['id']] = $c['name'];
}
if (count($categoryOptions) === 1) {
    echo '<div class="admin-card"><p class="text-muted mb-0">Add a job category first under <strong>Job Categories</strong>, then come back here to add its subcategories.</p></div>';
    return;
}

(new SimpleCrud('job_subcategories', [
    'category_id' => ['label' => 'Category', 'type' => 'select', 'options' => $categoryOptions, 'required' => true],
    'name'        => ['label' => 'Subcategory name', 'type' => 'text', 'required' => true],
    'sort_order'  => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
], 'sort_order, name', 'name'))->handle($pageTitle);
