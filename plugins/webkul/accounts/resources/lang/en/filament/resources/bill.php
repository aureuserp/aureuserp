<?php

return [
    'title' => 'Invoice',

    'navigation' => [
        'title' => 'Invoices',
        'group' => 'Invoices',
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
                    'vendor-bill'       => 'Vendor Bill',
                    'vendor'            => 'Vendor',
                    'bill-date'         => 'Bill Date',
                    'bill-reference'    => 'Bill Reference',
                    'accounting-date'   => 'Accounting Date',
                    'payment-reference' => 'Payment Reference',
                    'recipient-bank'    => 'Recipient Bank',
                    'due-date'          => 'Due Date',
                    'payment-term'      => 'Payment Term',
                    'journal'           => 'Journal',
                    'currency'          => 'Currency',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Invoice Lines',

                'repeater' => [
                    'products' => [
                        'title'       => 'Products',
                        'add-product' => 'Add Product',

                        'columns' => [
                            'product'             => 'Product',
                            'quantity'            => 'Quantity',
                            'unit'                => 'Unit',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount Percentage',
                            'unit-price'          => 'Unit Price',
                            'sub-total'           => 'Sub Total',
                        ],

                        'fields' => [
                            'product'             => 'Product',
                            'quantity'            => 'Quantity',
                            'unit'                => 'Unit',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount Percentage',
                            'unit-price'          => 'Unit Price',
                            'sub-total'           => 'Sub Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Other Information',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Accounting',

                        'fields' => [
                            'company'                 => 'Company',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Incoterm Location',
                            'payment-method'          => 'Payment Method',
                            'fiscal-position'         => 'Fiscal Position',
                            'fiscal-position-tooltip' => 'Fiscal positions are used to adapt taxes and accounts based on the customer location.',
                            'cash-rounding'           => 'Cash Rounding Method',
                            'cash-rounding-tooltip'   => 'Specifies the smallest cash-payable unit of the currency.',
                            'auto-post'               => 'Auto Post',
                            'checked'                 => 'Checked',
                        ],
                    ],
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
            'number'           => 'Number',
            'state'            => 'State',
            'customer'         => 'Customer',
            'bill-date'     => 'Bill Date',
            'checked'          => 'Checked',
            'accounting-date'  => 'Accounting',
            'due-date'         => 'Due Date',
            'source-document'  => 'Source Document',
            'reference'        => 'Reference',
            'sales-person'     => 'Sales Person',
            'tax-excluded'     => 'Tax Excluded',
            'tax'              => 'Tax',
            'total'            => 'Total',
            'amount-due'       => 'Amount Due',
            'bill-currency' => 'Bill Currency',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Name',
            'bill-partner-display-name'    => 'Bill Partner Display Name',
            'bill-date'                    => 'Bill Date',
            'checked'                      => 'Checked',
            'date'                         => 'Date',
            'bill-due-date'                => 'Bill Due Date',
            'bill-origin'                  => 'Bill Origin',
            'sales-person'                 => 'Sales Person',
            'currency'                     => 'Currency',
            'created-at'                   => 'Created At',
            'updated-at'                   => 'Updated At',
        ],

        'filters' => [
            'number'                       => 'Number',
            'bill-partner-display-name'    => 'Bill Partner Display Name',
            'bill-date'                    => 'Bill Date',
            'bill-due-date'                => 'Bill Due Date',
            'bill-origin'                  => 'Bill Origin',
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
                    'vendor-invoice'    => 'Vendor Invoice',
                    'vendor'            => 'Vendor',
                    'bill-date'         => 'Bill Date',
                    'bill-reference'    => 'Bill Reference',
                    'accounting-date'   => 'Accounting Date',
                    'payment-reference' => 'Payment Reference',
                    'recipient-bank'    => 'Recipient Bank',
                    'due-date'          => 'Due Date',
                    'payment-term'      => 'Payment Term',
                    'journal'           => 'Journal',
                    'currency'          => 'Currency',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Invoice Lines',

                'repeater' => [
                    'products' => [
                        'title'       => 'Products',
                        'add-product' => 'Add Product',

                        'entries' => [
                            'product'             => 'Product',
                            'quantity'            => 'Quantity',
                            'unit'                => 'Unit',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount Percentage',
                            'unit-price'          => 'Unit Price',
                            'sub-total'           => 'Sub Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Other Information',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Accounting',

                        'entries' => [
                            'company'           => 'Company',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Location',
                            'payment-method'    => 'Payment Method',
                            'checked'           => 'Checked',
                            'fiscal-position'   => 'Fiscal Position',
                            'cash-rounding'     => 'Cash Rounding Method',
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
