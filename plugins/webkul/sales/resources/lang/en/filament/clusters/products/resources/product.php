<?php

return [
    'navigation' => [
        'title' => 'Products',
        'group' => 'Inventory',
    ],


    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'tags'     => 'Tags',
                    'sales'    => 'Sales',
                    'purchase' => 'Purchase',
                ],
            ],

            'invoice-policy' => [
                'title' => 'Invoice Policy',
                'ordered-policy' => 'You can invoice goods before they are delivered.',
                'delivered-policy' => 'Invoice after delivery, based on quantities delivered, not ordered.',
            ],

            'pricing' => [
                'fields' => [
                    'taxes' => 'Taxes',
                    'purchase-unit' => 'Purchase Unit',
                    'purchase-per-in' => 'Per In',
                ],
            ],
        ],
    ],


    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'purchase-unit' => 'Purchase Unit',
                    'purchase-per-in' => 'Per In',
                ]
            ]
        ],
    ],
];
