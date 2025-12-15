<?php

return [
    'title' => 'Fiscal Positions',

    'navigation' => [
        'title' => 'Fiscal Positions',
        'group' => 'Accounting',
    ],

    'global-search' => [
        'zip-from' => 'Zip From',
        'zip-to'   => 'Zip To',
        'name'     => 'Name',
    ],

    'form' => [
        'fields' => [
            'name'                 => 'Name',
            'foreign-vat'          => 'Foreign VAT',
            'country'              => 'Country',
            'country-group'        => 'Country Group',
            'zip-from'             => 'Zip From',
            'zip-to'               => 'Zip To',
            'detect-automatically' => 'Detect Automatically',
            'notes'                => 'Notes',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Name',
            'company'              => 'Company',
            'country'              => 'Country',
            'country-group'        => 'Country Group',
            'created-by'           => 'Created By',
            'zip-from'             => 'Zip From',
            'zip-to'               => 'Zip To',
            'status'               => 'Status',
            'detect-automatically' => 'Detect Automatically',
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
                    'title' => 'Fiscal Position deleted',
                    'body'  => 'The fiscal Position has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Name',
            'foreign-vat'          => 'Foreign VAT',
            'country'              => 'Country',
            'country-group'        => 'Country Group',
            'zip-from'             => 'Zip From',
            'zip-to'               => 'Zip To',
            'detect-automatically' => 'Detect Automatically',
            'notes'                => 'Notes',
        ],
    ],
];
