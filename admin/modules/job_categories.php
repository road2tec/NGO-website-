<?php
(new SimpleCrud('job_categories', [
    'name'       => ['label' => 'Category name', 'type' => 'text', 'required' => true],
    'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
], 'sort_order, name', 'name'))->handle($pageTitle);
