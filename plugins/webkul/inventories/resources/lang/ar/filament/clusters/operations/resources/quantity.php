<?php

return [
    'navigation' => [
        'title' => 'Quantities',
        'group' => 'Adjustments',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Location',
            'product'          => 'Product',
            'package'          => 'Package',
            'lot'              => 'Lot / Serial Numbers',
            'counted-qty'      => 'Counted Quantity',
            'scheduled-at'     => 'Scheduled At',
            'storage-category' => 'Storage Category',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Location',
            'product'            => 'Product',
            'product-category'   => 'Product Category',
            'lot'                => 'Lot / Serial Numbers',
            'storage-category'   => 'Storage Category',
            'available-quantity' => 'Available Quantity',
            'quantity'           => 'Quantity',
            'package'            => 'Package',
            'last-counted-at'    => 'Last Counted At',
            'on-hand'            => 'On Hand Quantity',
            'counted'            => 'Counted Quantity',
            'difference'         => 'Difference',
            'scheduled-at'       => 'Scheduled At',
            'user'               => 'User',
            'company'            => 'Company',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantity updated',
                    'body'  => 'The quantity has been updated successfully.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Product',
            'product-category' => 'Product Category',
            'location'         => 'Location',
            'storage-category' => 'Storage Category',
            'lot'              => 'Lot / Serial Numbers',
            'company'          => 'Company',
            'package'          => 'Package',
        ],

        'filters' => [
            'product'             => 'Product',
            'uom'                 => 'Unit of Measure',
            'product-category'    => 'Product Category',
            'location'            => 'Location',
            'storage-category'    => 'Storage Category',
            'lot'                 => 'Lot / Serial Numbers',
            'company'             => 'Company',
            'package'             => 'Package',
            'on-hand-quantity'    => 'On Hand Quantity',
            'difference-quantity' => 'Difference Quantity',
            'incoming-at'         => 'Incoming At',
            'scheduled-at'        => 'Scheduled At',
            'user'                => 'User',
            'created-at'          => 'Created At',
            'updated-at'          => 'Updated At',
            'company'             => 'Company',
            'creator'             => 'Creator',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Add Quantity',

                'notification' => [
                    'title' => 'Quantity added',
                    'body'  => 'The quantity has been added successfully.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Quantity already exists',
                        'body'  => 'A quantity already exists for this configuration. Please update the existing quantity instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Apply',

                'notification' => [
                    'title' => 'Quantity changes applied',
                    'body'  => 'The quantity changes has been applied successfully.',
                ],
            ],

            'clear' => [
                'label' => 'Clear',

                'notification' => [
                    'title' => 'Quantity changes cleared',
                    'body'  => 'The quantity changes have been cleared successfully.',
                ],
            ],
        ],
    ],
];
