<?php

return [
    'title' => 'Payment',

    'navigation' => [
        'title' => 'Payments',
        'group' => 'Invoices',
    ],

    'global-search' => [
        'partner' => 'Partner',
        'amount'  => 'Amount',
        'date'    => 'Date',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Payment Type',
                'memo'                  => 'Memo',
                'date'                  => 'Date',
                'amount'                => 'Amount',
                'currency'              => 'Currency',
                'payment-method'        => 'Payment Method',
                'customer'              => 'Customer',
                'vendor'                => 'Vendor',
                'journal'               => 'Journal',
                'customer-bank-account' => 'Customer Bank Account',
                'vendor-bank-account'   => 'Vendor Bank Account',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Name',
            'date'            => 'Date',
            'journal'         => 'Journal',
            'payment-method'  => 'Payment Method',
            'partner'         => 'Partner',
            'amount-currency' => 'Amount (Currency)',
            'amount'          => 'Amount',
            'state'           => 'State',
            'company'         => 'Company',
            'currency'        => 'Currency',
            'created-by'      => 'Created By',
        ],

        'groups' => [
            'name'                             => 'Name',
            'company'                          => 'Company',
            'journal'                          => 'Journal',
            'partner'                          => 'Partner',
            'payment-method-line'              => 'Payment Method Line',
            'payment-method'                   => 'Payment Method',
            'partner-bank-account'             => 'Partner Bank Account',
            'created-at'                       => 'Created At',
            'updated-at'                       => 'Updated At',
        ],

        'filters' => [
            'company'                          => 'Company',
            'journal'                          => 'Journal',
            'customer-bank-account'            => 'Customer Bank Account',
            'payment-method'                   => 'Payment Method',
            'currency'                         => 'Currency',
            'partner'                          => 'Partner',
            'payment-method-line'              => 'Payment Method Line',
            'created-at'                       => 'Created At',
            'updated-at'                       => 'Updated At',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Payment deleted',
                    'body'  => 'The payment has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Payments deleted',
                    'body'  => 'The payments has been deleted successfully.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Export',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'Payment Information',
                'entries' => [
                    'state'                 => 'State',
                    'vendor'                => 'Vendor',
                    'customer'              => 'Customer',
                    'payment-type'          => 'Payment Type',
                    'journal'               => 'Journal',
                    'customer-bank-account' => 'Customer Bank Account',
                    'vendor-bank-account'   => 'Vendor Bank Account',
                    'amount'                => 'Amount',
                    'payment-method'        => 'Payment Method',
                    'date'                  => 'Date',
                    'memo'                  => 'Memo',
                ],
            ],
        ],
    ],

];
