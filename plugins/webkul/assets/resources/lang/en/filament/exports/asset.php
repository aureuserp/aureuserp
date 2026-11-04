<?php

return [
    'columns' => [
        'asset-tag'          => 'Asset Tag',
        'product'            => 'Product',
        'product-type'       => 'Product Type',
        'initial-quantity'   => 'Initial Quantity',
        'quantity'           => 'Quantity',
        'model'              => 'Model',
        'category'           => 'Category',
        'serial-no'          => 'Serial Number',
        'status'             => 'Status',
        'condition'          => 'Condition',
        'warehouse'          => 'Warehouse',
        'current-employee'   => 'Current Employee',
        'department'         => 'Department',
        'current-site'       => 'Current Site',
        'site-type'          => 'Site Type',
        'issued-at'          => 'Issued At',
        'expected-return-at' => 'Expected Return At',
        'cost'               => 'Cost',
        'purchase-date'      => 'Purchase Date',
        'warranty-until'     => 'Warranty Until',
        'custody-note'       => 'Custody Note',
        'note'               => 'Note',
        'company'            => 'Company',
        'created-at'         => 'Created At',
    ],

    'options' => [
        'section'    => 'Filter Exported Assets (Optional)',
        'products'   => 'Products',
        'statuses'   => 'Statuses',
        'conditions' => 'Conditions',
        'categories' => 'Categories',
        'warehouses' => 'Warehouses',
        'sites'      => 'Sites',
        'employees'  => 'Employees',
    ],

    'notification' => [
        'completed' => 'Your asset export has completed and :count row(s) exported.',
        'failed'    => ':count row(s) failed to export.',
    ],
];
