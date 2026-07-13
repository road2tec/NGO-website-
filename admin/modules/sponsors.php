<?php
(new SimpleCrud('sponsors', [
    'name'       => ['label' => 'Name', 'type' => 'text', 'required' => true],
    'logo'       => ['label' => 'Logo', 'type' => 'image', 'subdir' => 'sponsors'],
    'website'    => ['label' => 'Website URL', 'type' => 'text'],
    'type'       => ['label' => 'Type', 'type' => 'select', 'options' => ['csr' => 'CSR Partner', 'sponsor' => 'Sponsor', 'government' => 'Government'], 'required' => true],
    'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
], 'sort_order', 'name'))->handle($pageTitle);
