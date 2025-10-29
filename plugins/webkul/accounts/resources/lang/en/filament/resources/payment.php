<?php

return [
    'title' => 'Payment',

    'navigation' => [
        'title' => 'Payments',
        'group' => 'Invoices',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Payment Type',
                'memo'                  => 'Memo',
                'date'                  => 'Date',
                'amount'                => 'Amount',
                'payment-method'        => 'Payment Method',
                'customer'              => 'Customer',
                'journal'               => 'Journal',
                'customer-bank-account' => 'Customer Bank Account',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                             => 'Name',
            'company'                          => 'Company',
            'bank-account-holder'              => 'Bank Account Holder',
            'paired-internal-transfer-payment' => 'Paired Internal Transfer Payment',
            'payment-method-line'              => 'Payment Method Line',
            'payment-method'                   => 'Payment Method',
            'currency'                         => 'Currency',
            'partner'                          => 'Partner',
            'outstanding-amount'               => 'Outstanding Amount',
            'destination-account'              => 'Destination Account',
            'created-by'                       => 'Created By',
            'payment-transaction'              => 'Payment Transaction',
        ],

        'groups' => [
            'name'                             => 'Name',
            'company'                          => 'Company',
            'partner'                          => 'Partner',
            'payment-method-line'              => 'Payment Method Line',
            'payment-method'                   => 'Payment Method',
            'partner-bank-account'             => 'Partner Bank Account',
            'paired-internal-transfer-payment' => 'Paired Internal Transfer Payment',
            'created-at'                       => 'Created At',
            'updated-at'                       => 'Updated At',
        ],

        'filters' => [
            'company'                          => 'Company',
            'customer-bank-account'            => 'Customer Bank Account',
            'paired-internal-transfer-payment' => 'Paired Internal Transfer Payment',
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
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'Payment Information',
                'entries' => [
                    'state'                 => 'State',
                    'payment-type'          => 'Payment Type',
                    'journal'               => 'Journal',
                    'customer-bank-account' => 'Customer Bank Account',
                    'customer'              => 'Customer',
                ],
            ],

            'payment-details' => [
                'title'   => 'Payment Details',
                'entries' => [
                    'amount' => 'Amount',
                    'date'   => 'Date',
                    'memo'   => 'Memo',
                ],
            ],

            'payment-method' => [
                'title'   => 'Payment Method',
                'entries' => [
                    'payment-method' => 'Payment Method',
                ],
            ],
        ],
    ],

];
