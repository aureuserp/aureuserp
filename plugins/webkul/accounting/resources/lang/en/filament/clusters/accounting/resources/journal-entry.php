<?php

return [
    'title' => 'Journal Entries',

    'navigation' => [
        'title' => 'Journal Entries',
    ],

    'global-search' => [
        'number'           => 'Number',
        'customer'         => 'Customer',
        'invoice-date'     => 'Invoice Date',
        'invoice-due-date' => 'Invoice Due Date',
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
                    'add-item' => 'Add Item',

                    'columns' => [
                        'account'                  => 'Account',
                        'partner'                  => 'Partner',
                        'label'                    => 'Label',
                        'amount-currency'          => 'Amount (Currency)',
                        'currency'                 => 'Currency',
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
                        'debit'                    => 'Debit',
                        'credit'                   => 'Credit',
                        'discount-amount-currency' => 'Discount Amount (Currency)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Other Information',

                'fields' => [
                    'checked' => 'Checked',
                    'company' => 'Company',
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
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'customer-invoice' => 'Customer Invoice',
                    'customer'         => 'Customer',
                    'invoice-date'     => 'Invoice Date',
                    'due-date'         => 'Due Date',
                    'payment-term'     => 'Payment Term',
                    'journal'          => 'Journal',
                    'currency'         => 'Currency',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Invoice Lines',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Product',
                            'quantity'            => 'Quantity',
                            'unit'                => 'Unit Of Measure',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount Percentage',
                            'unit-price'          => 'Unit Price',
                            'sub-total'           => 'Sub Total',
                            'total'               => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Other Information',

                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Invoice',

                        'entries' => [
                            'customer-reference' => 'Customer Reference',
                            'sales-person'       => 'Sales Person',
                            'payment-reference'  => 'Payment Reference',
                            'recipient-bank'     => 'Recipient Bank',
                            'delivery-date'      => 'Delivery Date',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Accounting',

                        'entries' => [
                            'company'           => 'Company',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Location',
                            'payment-method'    => 'Payment Method',
                            'cash-rounding'     => 'Cash Rounding Method',
                            'fiscal-position'   => 'Fiscal Position',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
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
