<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'  => 'Name',
                    'code'  => 'Bank Identifier Code',
                    'email' => 'Email',
                    'phone' => 'Phone',
                ],
            ],

            'address' => [
                'title' => 'Address',

                'fields' => [
                    'address' => 'Address',
                    'city'    => 'City',
                    'street1' => 'Street 1',
                    'street2' => 'Street 2',
                    'state'   => 'State',
                    'zip'     => 'Zip',
                    'country' => 'Country',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Name',
            'code'           => 'Bank Identifier Code',
            'country'        => 'Country',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
            'deleted-at'     => 'Deleted At',
        ],

        'groups' => [
            'country'               => 'Country',
            'created-at'            => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Bank updated',
                    'body'  => 'The bank has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Bank restored',
                    'body'  => 'The bank has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bank deleted',
                    'body'  => 'The bank has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bank force deleted',
                    'body'  => 'The bank has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Banks restored',
                    'body'  => 'The banks has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banks deleted',
                    'body'  => 'The banks has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banks force deleted',
                    'body'  => 'The banks has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
