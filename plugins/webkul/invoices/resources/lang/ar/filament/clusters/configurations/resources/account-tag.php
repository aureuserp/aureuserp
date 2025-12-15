<?php

return [
    'title' => 'Account Tags',

    'navigation' => [
        'title' => 'Account Tags',
        'group' => 'Accounting',
    ],

    'global-search' => [
        'country' => 'Country',
        'name'    => 'Name',
    ],

    'form' => [
        'fields' => [
            'color'         => 'Color',
            'country'       => 'Country',
            'applicability' => 'Applicability',
            'name'          => 'Name',
            'status'        => 'Status',
            'tax-negate'    => 'Tax Negate',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Color',
            'country'       => 'Country',
            'created-by'    => 'Created By',
            'applicability' => 'Applicability',
            'name'          => 'Name',
            'status'        => 'Status',
            'tax-negate'    => 'Tax Negate',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
            'deleted-at'    => 'Deleted At',
        ],

        'filters' => [
            'bank'           => 'Bank',
            'account-holder' => 'Account Holder',
            'creator'        => 'Creator',
            'can-send-money' => 'Can Send Money',
        ],

        'groups' => [
            'country'       => 'Country',
            'created-by'    => 'Created By',
            'applicability' => 'Applicability',
            'name'          => 'Name',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Account Tag updated',
                    'body'  => 'The account Tag has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Account Tag deleted',
                    'body'  => 'The account Tag has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Account Tags deleted',
                    'body'  => 'The account Tags has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Color',
            'country'       => 'Country',
            'applicability' => 'Applicability',
            'name'          => 'Name',
            'status'        => 'Status',
            'tax-negate'    => 'Tax Negate',
        ],
    ],
];
