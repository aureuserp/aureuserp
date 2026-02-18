<?php

return [
    'title' => 'Manage Default Accounts',

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
