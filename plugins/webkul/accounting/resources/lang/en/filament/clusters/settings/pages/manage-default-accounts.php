<?php

return [
    'title' => 'Manage Default Accounts',

    'setup' => [
        'action'       => 'Set up accounting for this company',
        'notice'       => 'Accounting is not set up for this company yet. Use "Set up accounting" above to create its chart of accounts, journals and default settings.',
        'notification' => [
            'title' => 'Accounting set up',
            'body'  => 'Chart of accounts, journals and default settings were created for this company.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Exchange Difference Entries',

            'fields' => [
                'journal' => [
                    'label' => 'Journal',
                ],

                'gain' => [
                    'label' => 'Gain',
                ],

                'loss' => [
                    'label' => 'Loss',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Bank Transfer and Payments',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Bank Suspense Account',
                ],

                'transfer-account' => [
                    'label' => 'Transfer Account',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Product Accounts',

            'fields' => [
                'income-account' => [
                    'label' => 'Income Account',
                ],

                'expense-account' => [
                    'label' => 'Expense Account',
                ],
            ],
        ],
    ],
];
