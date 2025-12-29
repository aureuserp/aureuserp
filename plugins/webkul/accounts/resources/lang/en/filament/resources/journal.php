<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Journal Entries',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Accounting Information',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Credit Note Sequence',
                            'dedicated-payment-sequence'     => 'Dedicated Payment Sequence',
                            'sort-code-placeholder'          => 'Enter the journal code',
                            'sort-code'                      => 'Sort',
                            'currency'                       => 'Currency',
                            'color'                          => 'Color',
                            'default-account'                => 'Default Account',
                            'profit-account'                 => 'Profit Account',
                            'loss-account'                   => 'Loss Account',
                            'suspense-account'               => 'Suspense Account',
                            'bank-account'                   => 'Bank Account',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Bank Account Number',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Incoming Payments',

                'fields' => [
                    'relation-notes'             => 'Relation Notes',
                    'relation-notes-placeholder' => 'Enter any relation details',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Outgoing Payments',

                'fields' => [
                    'relation-notes'             => 'Relation Notes',
                    'relation-notes-placeholder' => 'Enter any relation details',
                ],
            ],
            'advanced-settings' => [
                'title'  => 'Advanced Settings',
                'fields' => [
                    'allowed-accounts'       => 'Allowed Accounts',
                    'control-access'         => 'Control Access',
                    'payment-communication'  => 'Payment Communication',
                    'auto-check-on-post'     => 'Auto Check on Post',
                    'communication-type'     => 'Communication Type',
                    'communication-standard' => 'Communication Standard',
                ],
            ],
        ],

        'general' => [
            'title' => 'General Information',

            'fields' => [
                'name'    => 'Name',
                'type'    => 'Type',
                'company' => 'Company',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'type'       => 'Type',
            'code'       => 'Code',
            'currency'   => 'Currency',
            'created-by' => 'Created By',
            'status'     => 'Status',
        ],

        'relation-managers' => [
            'moves-relation-manager' => [
                'columns' => [
                    'journal-entry' => 'Journal Entry',
                    'account'       => 'Account',
                    'partner'       => 'Partner',
                    'label'         => 'Label',
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Incoterm deleted',
                    'body'  => 'The incoterm has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Journal deleted',
                    'body'  => 'The journal has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Journal Entries',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Accounting Information',
                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Credit Note Sequence',
                            'dedicated-payment-sequence'     => 'Dedicated Payment Sequence',
                            'sort-code-placeholder'          => 'Enter the journal code',
                            'sort-code'                      => 'Sort',
                            'currency'                       => 'Currency',
                            'color'                          => 'Color',
                            'default-account'                => 'Default Account',
                            'profit-account'                 => 'Profit Account',
                            'loss-account'                   => 'Loss Account',
                            'suspense-account'               => 'Suspense Account',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Bank Account Number',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Incoming Payments',

                'entries' => [
                    'relation-notes'             => 'Relation Notes',
                    'relation-notes-placeholder' => 'Enter any relation details',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Outgoing Payments',

                'entries' => [
                    'relation-notes'             => 'Relation Notes',
                    'relation-notes-placeholder' => 'Enter any relation details',
                ],
            ],
            'advanced-settings' => [
                'title'   => 'Advanced Settings',
                'entries' => [
                    'allowed-accounts'       => 'Allowed Accounts',
                    'control-access'         => 'Control Access',
                    'payment-communication'  => 'Payment Communication',
                    'auto-check-on-post'     => 'Auto Check on Post',
                    'communication-type'     => 'Communication Type',
                    'communication-standard' => 'Communication Standard',
                ],
            ],
        ],

        'general' => [
            'title' => 'General Information',

            'entries' => [
                'name'    => 'Name',
                'type'    => 'Type',
                'company' => 'Company',
            ],
        ],
    ],

];
