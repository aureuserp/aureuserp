<?php

return [
    'title' => 'Quantities',

    'tabs' => [
        'internal-locations' => 'Internal Locations',
        'transit-locations'  => 'Transit Locations',
        'on-hand'            => 'On Hand',
        'to-count'           => 'To Count',
        'to-apply'           => 'To Apply',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Product',
            'location'         => 'Location',
            'package'          => 'Package',
            'lot'              => 'Lot / Serial Numbers',
            'on-hand-qty'      => 'On Hand Quantity',
            'storage-category' => 'Storage Category',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Product',
            'location'          => 'Location',
            'lot'               => 'Lot / Serial Numbers',
            'storage-category'  => 'Storage Category',
            'quantity'          => 'Quantity',
            'package'           => 'Package',
            'on-hand'           => 'On Hand Quantity',
            'reserved-quantity' => 'Reserved Quantity',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantity updated',
                    'body'  => 'The quantity has been updated successfully.',
                ],
            ],
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
                        'body'  => 'Already has a quantity for the same configuration. Please update the quantity instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Quantity deleted',
                    'body'  => 'The quantity has been deleted successfully.',
                ],
            ],
        ],
    ],
];
