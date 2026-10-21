<?php

return [
    'navigation' => [
        'title' => 'Coins/Bills',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'                           => 'Name',
                    'value'                          => 'Coin/Bill Value',
                    'is-for-all-configs'             => 'For All PoS',
                    'is-for-all-configs-helper-text' => 'If checked, this coin/bill will be available in all points of sale.',
                    'configs'                        => 'Point of Sales',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Name',
            'value'              => 'Coin/Bill Value',
            'is-for-all-configs' => 'For All PoS',
            'configs'            => 'Point of Sales',
            'company'            => 'Company',
        ],
    ],
];
