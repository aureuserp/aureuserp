<?php

return [
    'title' => 'Tax Groups',

    'navigation' => [
        'title' => 'Tax Groups',
        'group' => 'Accounting',
    ],

    'global-search' => [
        'company-name' => 'Company Name',
        'payment-term' => 'Payment Term',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Company',
                'country'            => 'Country',
                'name'               => 'Name',
                'preceding-subtotal' => 'Preceding Subtotal',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Company',
            'country'            => 'Country',
            'created-by'         => 'Created By',
            'name'               => 'Name',
            'preceding-subtotal' => 'Preceding Subtotal',
            'created-at'         => 'Created At',
            'updated-at'         => 'Updated At',
        ],

        'groups' => [
            'name'       => 'Name',
            'company'    => 'Company',
            'country'    => 'Country',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Payment Term deleted',
                    'body'  => 'The payment term has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tax Groups deleted',
                    'body'  => 'The tax Groups has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Company',
                'country'            => 'Country',
                'name'               => 'Name',
                'preceding-subtotal' => 'Preceding Subtotal',
            ],
        ],
    ],
];
