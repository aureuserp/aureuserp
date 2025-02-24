<?php

return [
    'title' => 'Invoices',

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
        'tabs' => [
            'products' => [
                'title' => 'Products',

                'repeater' => [
                    'products' => [
                        'title'            => 'Products',
                        'add-product-line' => 'Add Product Line',

                        'fields' => [
                            'product'             => 'Product',
                            'quantity'            => 'Quantity',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount (%)',
                            'unit-price'          => 'Unit Price',
                            'sub-total'           => 'Sub Total',
                            'total'               => 'Total',
                        ],
                    ],
                    'section' => [
                        'title' => 'Add a section',
                    ],

                    'note' => [
                        'title' => 'Add a note',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Other Information',

                'fields' => [
                    'fieldset' => [
                        'invoice' => [
                            'title'  => 'Invoice',
                            'fields' => [
                                'customer-reference' => 'Customer Reference',
                                'sale-person'        => 'Sale Person',
                                'sales-team'         => 'Sales Team',
                                'recipient-bank'     => 'Recipient Bank',
                                'payment-reference'  => 'Payment Reference',
                                'delivery-date'      => 'Delivery Date',
                                'sales-person'       => 'Sales Person',
                            ],
                        ],

                        'accounting' => [
                            'title'  => 'Accounting',
                            'fields' => [
                                'incoterm'          => 'Incoterm',
                                'incoterm-location' => 'Incoterm Location',
                                'fiscal-position'   => 'Fiscal Position',
                                'payment-method'    => 'Payment Method',
                                'auto-post'         => 'Auto Post',
                                'checked'           => 'Checked',
                            ],
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',

                'fields' => [
                    'narration' => 'Term & Conditions',
                ],
            ],
        ],

        'section' => [
            'fieldset' => [
                'general' => [
                    'title' => 'General',

                    'fields' => [
                        'customer' => 'Customer',
                    ],
                ],

                'invoice-date-and-payment-term' => [
                    'title' => 'Invoice Date & Payment Term',

                    'fields' => [
                        'invoice-date'     => 'Invoice Date',
                        'due-date'         => 'Due Date',
                        'payment-term'     => 'Payment Term',
                    ],
                ],

                'marketing' => [
                    'title' => 'Marketing',

                    'fields' => [
                        'campaign' => 'Campaign',
                        'medium'   => 'Medium',
                        'source'   => 'Source',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Number',
            'customer'         => 'Customer',
            'invoice-date'     => 'Invoice Date',
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
            'invoice-currency' => 'Invoice Currency',
        ],

        'groups' => [
            'name'                         => 'Name',
            'invoice-partner-display-name' => 'Invoice Partner Display Name',
            'invoice-date'                 => 'Invoice Date',
            'checked'                      => 'Checked',
            'date'                         => 'Date',
            'invoice-due-date'             => 'Invoice Due Date',
            'invoice-origin'               => 'Invoice Origin',
            'sales-person'                 => 'Sales Person',
            'currency'                     => 'Currency',
            'created-at'                   => 'Created At',
            'updated-at'                   => 'Updated At',
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
        'tabs' => [
            'products' => [
                'title' => 'Products',

                'repeater' => [
                    'products' => [
                        'title'   => 'Products',
                        'entries' => [
                            'product'    => 'Product',
                            'quantity'   => 'Quantity',
                            'unit-price' => 'Unit Price',
                            'total'      => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Other Information',

                'entries' => [
                    'fieldset' => [
                        'invoice' => [
                            'title' => 'Invoice',

                            'entries' => [
                                'customer-reference' => 'Customer Reference',
                                'sale-person'        => 'Sale Person',
                                'recipient-bank'     => 'Recipient Bank',
                                'payment-reference'  => 'Payment Reference',
                                'delivery-date'      => 'Delivery Date',
                                'sales-person'       => 'Sales Person',
                            ],
                        ],

                        'accounting' => [
                            'title'   => 'Accounting',
                            'entries' => [
                                'incoterm'          => 'Incoterm',
                                'incoterm-location' => 'Incoterm Location',
                                'fiscal-position'   => 'Fiscal Position',
                                'payment-method'    => 'Payment Method',
                                'auto-post'         => 'Auto Post',
                                'checked'           => 'Checked',
                            ],
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',

                'fields' => [
                    'narration' => 'Term & Conditions',
                ],
            ],
        ],

        'section' => [
            'fieldset' => [
                'general' => [
                    'title' => 'General',

                    'fields' => [
                        'customer' => 'Customer',
                        'address'  => 'Address',
                    ],
                ],

                'invoice-date-and-payment-term' => [
                    'title' => 'Invoice Date & Payment Term',

                    'fields' => [
                        'invoice-date'     => 'Invoice Date',
                        'due-date'         => 'Due Date',
                        'payment-term'     => 'Payment Term',
                    ],
                ],

                'marketing' => [
                    'title' => 'Marketing',

                    'fields' => [
                        'campaign' => 'Campaign',
                        'medium'   => 'Medium',
                        'source'   => 'Source',
                    ],
                ],
            ],
        ],
    ],
];
