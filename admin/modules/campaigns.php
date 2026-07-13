<?php
(new SimpleCrud('campaigns', [
    'title'         => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'slug'          => ['label' => 'URL slug (leave blank to auto-generate)', 'type' => 'text'],
    'summary'       => ['label' => 'Short summary', 'type' => 'textarea', 'rows' => 2],
    'description'   => ['label' => 'Full description', 'type' => 'textarea', 'rows' => 5],
    'image'         => ['label' => 'Cover image', 'type' => 'image', 'subdir' => 'campaigns'],
    'goal_amount'   => ['label' => 'Goal amount (₹)', 'type' => 'number', 'required' => true],
    'raised_amount' => ['label' => 'Raised so far (₹) - update as donations come in', 'type' => 'number', 'default' => 0],
    'start_date'    => ['label' => 'Start date', 'type' => 'date'],
    'end_date'      => ['label' => 'End date', 'type' => 'date'],
    'is_active'     => ['label' => 'Active', 'type' => 'checkbox', 'default' => 1],
], 'id DESC', 'title', 'title'))->handle($pageTitle);
