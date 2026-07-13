<?php
(new SimpleCrud('documents', [
    'title'      => ['label' => 'Title', 'type' => 'text', 'required' => true],
    'category'   => ['label' => 'Category', 'type' => 'text', 'default' => 'General'],
    'file'       => ['label' => 'File (PDF/DOC/image)', 'type' => 'file', 'subdir' => 'documents'],
    'is_visible' => ['label' => 'Visible on website', 'type' => 'checkbox', 'default' => 1],
], 'created_at DESC', 'title'))->handle($pageTitle);
