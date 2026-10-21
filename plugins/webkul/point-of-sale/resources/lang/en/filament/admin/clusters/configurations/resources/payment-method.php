<?php

return [
    'navigation' => [
        'title' => 'Payment Methods',
        'group' => 'Point of Sale',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'                             => 'Name',
                    'company'                          => 'Company',
                    'terminal-type'                    => 'Integration',
                    'is-split-transaction'             => 'Identify Customer',
                    'is-split-transaction-helper-text' => 'Post one journal item per payment on the customer receivable.',
                    'is-active'                        => 'Active',
                ],
            ],

            'accounting' => [
                'title' => 'Accounting',

                'fields' => [
                    'journal'             => 'Journal',
                    'journal-placeholder' => 'Leave empty to use the receivable account of the customer',
                    'account-placeholder' => 'Leave empty to use the default account from the company settings',
                    'receivable-account'  => 'Intermediary Account',
                    'outstanding-account' => 'Outstanding Account',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'                    => 'Name',
                    'type'                    => 'Type',
                    'terminal-type'           => 'Payment Terminal',
                    'journal-name'            => 'Journal',
                    'receivableAccount-name'  => 'Receivable Account',
                    'outstandingAccount-name' => 'Outstanding Account',
                    'is-cash-count'           => 'Counted in Cash Drawer',
                    'is-split-transaction'    => 'Split Transactions',
                    'is-active'               => 'Active',
                    'company-name'            => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Name',
            'type'               => 'Type',
            'journal'            => 'Journal',
            'configs'            => 'POS',
            'terminal-type'      => 'Integration',
            'receivable-account' => 'Intermediary Account',
            'is-cash-count'      => 'Cash Drawer',
            'is-active'          => 'Active',
            'company'            => 'Company',
        ],

        'groups' => [
            'type'    => 'Type',
            'company' => 'Company',
        ],

        'filters' => [
            'type' => 'Type',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'Payment method restored',
                        'body'  => 'The payment method has been restored.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Payment method deleted',
                        'body'  => 'The payment method has been deleted.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Payment method deleted permanently',
                        'body'  => 'The payment method has been deleted permanently.',
                    ],

                    'error' => [
                        'title' => 'Payment method could not be deleted',
                        'body'  => 'The payment method cannot be deleted permanently because it has payments. Archive it instead.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Payment methods deleted permanently',
                        'body'  => 'The selected payment methods have been deleted permanently.',
                    ],

                    'error' => [
                        'title' => 'Payment methods could not be deleted',
                        'body'  => 'One or more payment methods cannot be deleted permanently because they have payments. Archive them instead.',
                    ],
                ],
            ],
        ],
    ],

    'in-open-session' => [
        'title' => 'Payment method in use',
    ],
];
