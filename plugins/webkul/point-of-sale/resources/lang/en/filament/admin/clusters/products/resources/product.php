<?php

return [
    'navigation' => [
        'title' => 'Products',
    ],

    'tabs' => [
        'pos' => 'POS',
    ],

    'form' => [
        'sections' => [
            'point-of-sale' => [
                'title' => 'Point of Sale',

                'fields' => [
                    'available-in-pos'        => 'Available in POS',
                    'available-in-pos-helper' => 'Make this product available in the Point of Sale terminals.',
                    'categories'              => 'PoS Product Categories',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'point-of-sale' => [
                'title' => 'Point of Sale',

                'entries' => [
                    'available-in-pos' => 'Available in POS',
                    'categories'       => 'PoS Product Categories',
                ],
            ],
        ],
    ],
];
