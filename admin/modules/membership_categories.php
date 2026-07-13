<?php
(new SimpleCrud('membership_categories', [
    'name'             => ['label' => 'Category name', 'type' => 'text', 'required' => true],
    'fee'              => ['label' => 'Fee (₹)', 'type' => 'number', 'required' => true],
    'duration_months'  => ['label' => 'Duration (months, 1200 = lifetime)', 'type' => 'number', 'default' => 12],
    'benefits'         => ['label' => 'Benefits', 'type' => 'textarea', 'rows' => 3],
], 'fee', 'name'))->handle($pageTitle);
