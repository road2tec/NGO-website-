<?php
(new SimpleCrud('testimonials', [
    'name'       => ['label' => 'Name', 'type' => 'text', 'required' => true],
    'role'       => ['label' => 'Role / relationship', 'type' => 'text'],
    'photo'      => ['label' => 'Photo (optional)', 'type' => 'image', 'subdir' => 'people'],
    'message'    => ['label' => 'Testimonial', 'type' => 'textarea', 'rows' => 4, 'required' => true],
    'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
    'is_active'  => ['label' => 'Active', 'type' => 'checkbox', 'default' => 1],
], 'sort_order', 'name'))->handle($pageTitle);
