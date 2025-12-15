<?php

return [
    'title' => 'Locations',

    'table' => [
        'columns' => [
            'product'          => 'Product',
            'location'         => 'Location',
            'storage-category' => 'Storage Category',
            'quantity'         => 'Quantity',
            'package'          => 'Package',
            'on-hand'          => 'On Hand Quantity',
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
