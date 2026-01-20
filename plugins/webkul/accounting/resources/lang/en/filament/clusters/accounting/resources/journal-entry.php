<?php

return [
    'title' => 'Journal Entries',

    'navigation' => [
        'title' => 'Journal Entries',
    ],

    'record-sub-navigation' => [
        'payment' => 'Payment',
    ],

    'global-search' => [
        'number'   => 'Number',
        'partner'  => 'Partner',
        'date'     => 'Invoice Date',
        'due-date' => 'Invoice Due Date',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'reference'       => 'Reference',
                    'accounting-date' => 'Accounting Date',
                    'journal'         => 'Journal',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Journal Items',

                'repeater' => [
                    'title'       => 'Items',
                    'add-item'    => 'Add Item',

                    'columns' => [
                        'account'                  => 'Account',
                        'partner'                  => 'Partner',
                        'label'                    => 'Label',
                        'amount-currency'          => 'Amount (Currency)',
                        'currency'                 => 'Currency',
                        'taxes'                    => 'Taxes',
                        'debit'                    => 'Debit',
                        'credit'                   => 'Credit',
                        'discount-amount-currency' => 'Discount Amount (Currency)',
                    ],

                    'fields' => [
                        'account'                  => 'Account',
                        'partner'                  => 'Partner',
                        'label'                    => 'Label',
                        'amount-currency'          => 'Amount (Currency)',
                        'currency'                 => 'Currency',
                        'taxes'                    => 'Taxes',
                        'debit'                    => 'Debit',
                        'credit'                   => 'Credit',
                        'discount-amount-currency' => 'Discount Amount (Currency)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Other Information',

                'fields' => [
                    'checked'         => 'Checked',
                    'company'         => 'Company',
                    'fiscal-position' => 'Fiscal Position',
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Total',
        'columns' => [
            'invoice-date' => 'Invoice Date',
            'date'         => 'Date',
            'number'       => 'Number',
            'partner'      => 'Partner',
            'reference'    => 'Reference',
            'journal'      => 'Journal',
            'company'      => 'Company',
            'total'        => 'Total',
            'state'        => 'State',
            'checked'      => 'Checked',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'partner'        => 'Partner',
            'journal'        => 'Journal',
            'state'          => 'State',
            'payment-method' => 'Payment Method',
            'date'           => 'Date',
            'invoice-date'   => 'Invoice Date',
            'company'        => 'Company',
        ],

        'filters' => [
            'number'                       => 'Number',
            'invoice-partner-display-name' => 'Invoice Partner Display Name',
            'invoice-date'                 => 'Invoice Date',
            'invoice-due-date'             => 'Invoice Due Date',
            'invoice-origin'               => 'Invoice Origin',
            'reference'                    => 'Reference',
            'created-at'                   => 'Created At',
            'updated-at'                   => 'Updated At',
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
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'number'          => 'Number',
                    'reference'       => 'Reference',
                    'accounting-date' => 'Accounting Date',
                    'journal'         => 'Journal',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Journal Items',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Account',
                        'partner'  => 'Partner',
                        'label'    => 'Label',
                        'currency' => 'Currency',
                        'taxes'    => 'Taxes',
                        'debit'    => 'Debit',
                        'credit'   => 'Credit',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Other Information',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Accounting',

                        'entries' => [
                            'company'         => 'Company',
                            'fiscal-position' => 'Fiscal Position',
                            'checked'         => 'Checked',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

];
