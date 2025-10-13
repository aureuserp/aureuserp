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
                    'customer-invoice' => 'Customer Invoice',
                    'customer'         => 'Customer',
                    'invoice-date'     => 'Invoice Date',
                    'due-date'         => 'Due Date',
                    'payment-term'     => 'Payment Term',
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
                            'discount-percentage' => 'Discount',
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
                    'invoice' => [
                        'title'  => 'Invoice',
                        'fields' => [
                            'customer-reference' => 'Customer Reference',
                            'sales-person'       => 'Sales Person',
                            'payment-reference'  => 'Payment Reference',
                            'recipient-bank'     => 'Recipient Bank',
                            'delivery-date'      => 'Delivery Date',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Accounting',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Location',
                            'payment-method'    => 'Payment Method',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Additional Information',
                        'fields' => [
                            'company'  => 'Company',
                            'currency' => 'Currency',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campaign',
                            'medium'   => 'Medium',
                            'source'   => 'Source',
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
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'customer-invoice' => 'Customer Invoice',
                    'customer'         => 'Customer',
                    'invoice-date'     => 'Invoice Date',
                    'due-date'         => 'Due Date',
                    'payment-term'     => 'Payment Term',
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

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Location',
                            'payment-method'    => 'Payment Method',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Additional Information',

                        'entries' => [
                            'company'  => 'Company',
                            'currency' => 'Currency',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campaign',
                            'medium'   => 'Medium',
                            'source'   => 'Source',
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
