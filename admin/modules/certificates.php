<?php
(new SimpleCrud('org_certificates', [
    'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'description' => ['label' => 'Description', 'type' => 'text'],
    'file'        => ['label' => 'Certificate file (PDF/image)', 'type' => 'file', 'subdir' => 'documents'],
    'sort_order'  => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
], 'sort_order', 'title'))->handle($pageTitle);
