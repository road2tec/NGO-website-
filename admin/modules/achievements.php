<?php
(new SimpleCrud('achievements', [
    'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'year'        => ['label' => 'Year', 'type' => 'text'],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'rows' => 3],
    'image'       => ['label' => 'Image (optional)', 'type' => 'image', 'subdir' => 'achievements'],
    'sort_order'  => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
], 'sort_order', 'title'))->handle($pageTitle);
