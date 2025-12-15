<?php

return [
    'title' => 'Cash Rounding',

    'navigation' => [
        'title' => 'Cash Rounding',
        'group' => 'Management',
    ],

    'global-search' => [
        'name'     => 'Name',
    ],

    'form' => [
        'fields' => [
            'name'               => 'Name',
            'rounding-precision' => 'Rounding Precision',
            'rounding-strategy'  => 'Rounding Strategy',
            'profit-account'     => 'Profit Account',
            'loss-account'       => 'Loss Account',
            'rounding-method'    => 'Rounding Method',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Name',
            'rounding-strategy'    => 'Rounding Strategy',
            'rounding-method'      => 'Rounding Method',
            'created-by'           => 'Created By',
            'profit-account'       => 'Profit Account',
            'loss-account'         => 'Loss Account',
        ],

        'groups' => [
            'name'              => 'Name',
            'rounding-strategy' => 'Rounding Strategy',
            'rounding-method'   => 'Rounding Method',
            'created-by'        => 'Created By',
            'profit-account'    => 'Profit Account',
            'loss-account'      => 'Loss Account',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cash Rounding deleted',
                    'body'  => 'The cash rounding has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cash Rounding deleted',
                    'body'  => 'The cash rounding has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Name',
            'rounding-precision' => 'Rounding Precision',
            'rounding-strategy'  => 'Rounding Strategy',
            'profit-account'     => 'Profit Account',
            'loss-account'       => 'Loss Account',
            'rounding-method'    => 'Rounding Method',
        ],
    ],
];
