<?php
(new SimpleCrud('people', [
    'type'        => ['label' => 'Type', 'type' => 'select', 'options' => ['board' => 'Board Member', 'team' => 'Team Member'], 'required' => true],
    'name'        => ['label' => 'Name', 'type' => 'text', 'required' => true],
    'designation' => ['label' => 'Designation', 'type' => 'text'],
    'photo'       => ['label' => 'Photo', 'type' => 'image', 'subdir' => 'people'],
    'bio'         => ['label' => 'Short bio', 'type' => 'textarea', 'rows' => 3],
    'sort_order'  => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
    'is_active'   => ['label' => 'Active', 'type' => 'checkbox', 'default' => 1],
], 'sort_order', 'name'))->handle($pageTitle);
