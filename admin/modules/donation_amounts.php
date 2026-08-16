<?php
(new SimpleCrud('donation_amount_options', [
    'amount'     => ['label' => 'Amount (₹)', 'type' => 'number', 'required' => true],
    'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
    'is_active'  => ['label' => 'Active', 'type' => 'checkbox', 'default' => 1],
], 'sort_order, amount', 'amount'))->handle($pageTitle);
