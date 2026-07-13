<?php
(new SimpleCrud('about_sections', [
    'title'      => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'slug'       => ['label' => 'Slug (who-we-are, mission, vision, history, legal)', 'type' => 'text', 'required' => true],
    'content'    => ['label' => 'Content', 'type' => 'textarea', 'rows' => 6, 'required' => true],
    'image'      => ['label' => 'Image (optional)', 'type' => 'image', 'subdir' => 'about'],
    'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
], 'sort_order', 'title'))->handle($pageTitle);
