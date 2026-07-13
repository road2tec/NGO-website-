<?php
(new SimpleCrud('projects', [
    'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'slug'        => ['label' => 'URL slug (leave blank to auto-generate)', 'type' => 'text'],
    'type'        => ['label' => 'Type', 'type' => 'select', 'options' => [
                        'ongoing' => 'Ongoing', 'completed' => 'Completed', 'upcoming' => 'Upcoming',
                        'government' => 'Government', 'csr' => 'CSR'], 'required' => true],
    'summary'     => ['label' => 'Short summary', 'type' => 'textarea', 'rows' => 2],
    'description' => ['label' => 'Full description', 'type' => 'textarea', 'rows' => 6],
    'image'       => ['label' => 'Cover image', 'type' => 'image', 'subdir' => 'projects'],
    'location'    => ['label' => 'Location', 'type' => 'text'],
    'start_date'  => ['label' => 'Start date', 'type' => 'date'],
    'end_date'    => ['label' => 'End date (if completed)', 'type' => 'date'],
    'budget'      => ['label' => 'Budget (₹, optional)', 'type' => 'number'],
    'partner'     => ['label' => 'Partner organisation (optional)', 'type' => 'text'],
    'report_file' => ['label' => 'Project report (PDF, optional)', 'type' => 'file', 'subdir' => 'documents'],
    'is_featured' => ['label' => 'Featured on homepage', 'type' => 'checkbox'],
], 'id DESC', 'title', 'title'))->handle($pageTitle);
