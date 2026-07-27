<?php

return [
    'navigation' => [
        'title' => 'Reporting',
        'group' => 'Inventory',
    ],

    'moves' => [
        'navigation' => [
            'title' => 'Moves History',
        ],

        'filters' => [
            'product-category'     => 'Product Category',
            'source-location'      => 'Source Location',
            'destination-location' => 'Destination Location',
            'package'              => 'Package',
            'lot'                  => 'Lot/Serial Number',
            'package-type'         => 'Package Type',
        ],

        'groups' => [
            'product'   => 'Product',
            'status'    => 'Status',
            'date'      => 'Date',
            'operation' => 'Operation',
            'location'  => 'Location',
            'category'  => 'Category',
        ],
    ],

    'quantities' => [
        'navigation' => [
            'title' => 'Locations',
        ],

        'filters' => [
            'warehouse'        => 'Warehouse',
            'location'         => 'Location',
            'product-category' => 'Product Category',
            'storage-category' => 'Storage Category',
            'package'          => 'Package',
            'lot'              => 'Lot/Serial Number',
            'package-type'     => 'Package Type',
        ],

        'groups' => [
            'product'          => 'Product',
            'product-category' => 'Product Category',
            'location'         => 'Location',
            'storage-category' => 'Storage Category',
            'lot'              => 'Lot/Serial Number',
            'package'          => 'Package',
            'company'          => 'Company',
        ],
    ],
];
