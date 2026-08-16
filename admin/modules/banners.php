<?php
(new SimpleCrud('banners', [
    'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'subtitle'    => ['label' => 'Subtitle', 'type' => 'textarea', 'rows' => 2],
    'image'       => ['label' => 'Banner image', 'type' => 'image', 'subdir' => 'banners'],
    'button_text' => ['label' => 'Button text', 'type' => 'text'],
    'button_link' => ['label' => 'Button link (internal path e.g. "donate", or full URL e.g. "https://example.com")', 'type' => 'text'],
    'sort_order'  => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
    'is_active'   => ['label' => 'Active', 'type' => 'checkbox', 'default' => 1],
], 'sort_order', 'title'))->handle($pageTitle);
