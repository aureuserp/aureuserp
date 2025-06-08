<?php

return [
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
                    'success' => [
                        'title' => 'Tax Group deleted',
                        'body'  => 'The tax group has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Tax Group could not be deleted',
                        'body'  => 'The tax group cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tax Groups deleted',
                        'body'  => 'The tax groups has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Tax Groups could not be deleted',
                        'body'  => 'The tax groups cannot be deleted because they are currently in use.',
                    ],
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
