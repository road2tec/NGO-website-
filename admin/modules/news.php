<?php
(new SimpleCrud('news', [
    'title'        => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'slug'         => ['label' => 'URL slug (leave blank to auto-generate)', 'type' => 'text'],
    'category'     => ['label' => 'Category', 'type' => 'text', 'default' => 'Updates'],
    'image'        => ['label' => 'Cover image', 'type' => 'image', 'subdir' => 'news'],
    'excerpt'      => ['label' => 'Short excerpt', 'type' => 'textarea', 'rows' => 2],
    'content'      => ['label' => 'Full content', 'type' => 'textarea', 'rows' => 6, 'required' => true],
    'is_featured'  => ['label' => 'Featured on homepage', 'type' => 'checkbox'],
    'is_published' => ['label' => 'Published', 'type' => 'checkbox', 'default' => 1],
], 'published_at DESC', 'title', 'title'))->handle($pageTitle);
