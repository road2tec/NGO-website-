<?php
(new SimpleCrud('homepage_buttons', [
    'label'      => ['label' => 'Button text', 'type' => 'text', 'required' => true],
    'url'        => ['label' => 'URL - internal path (e.g. "about" or "donate") or a full https:// link', 'type' => 'text', 'required' => true, 'placeholder' => 'https://example.org or about'],
    'style'      => ['label' => 'Style', 'type' => 'select', 'options' => ['outline' => 'Outline', 'primary' => 'Filled (primary)']],
    'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
    'is_active'  => ['label' => 'Active', 'type' => 'checkbox', 'default' => 1],
], 'sort_order, id', 'label'))->handle($pageTitle);
