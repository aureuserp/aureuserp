<?php

return [
    'navigation' => [
        'title' => 'Bank Accounts',
        'group' => 'Banks',
    ],

    'form' => [
        'account-number'     => 'Account Number',
        'bank'               => [
            'title'    => 'Bank',
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

        'account-holder'     => 'Account Holder',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Account Number',
            'bank'           => 'Bank',
            'account-holder' => 'Account Holder',
            'send-money'     => 'Can Send Money',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
            'deleted-at'     => 'Deleted At',
        ],

        'filters' => [
            'bank'           => 'Bank',
            'account-holder' => 'Account Holder',
            'creator'        => 'Creator',
            'can-send-money' => 'Can Send Money',
        ],

        'groups' => [
            'bank'               => 'Bank',
            'can-send-money'     => 'Can Send Money',
            'created-at'         => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Bank account updated',
                    'body'  => 'The bank account has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Bank account restored',
                    'body'  => 'The bank account has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bank account deleted',
                    'body'  => 'The bank account has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bank account force deleted',
                    'body'  => 'The bank account has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bank accounts restored',
                    'body'  => 'The bank accounts has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bank accounts deleted',
                    'body'  => 'The bank accounts has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bank accounts force deleted',
                    'body'  => 'The bank accounts has been force deleted successfully.',
                ],
            ],
        ],
    ],
];
