<?php

return [
    'title' => 'Invoice',

    'navigation' => [
        'title' => 'Invoices',
        'group' => 'Invoices',
    ],

    'global-search' => [
        'customer' => 'Customer',
        'date'     => 'Date',
        'due-date' => 'Due Date',
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

                        'fields' => [
                            'company'                 => 'Company',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Incoterm Location',
                            'fiscal-position'         => 'Fiscal Position',
                            'fiscal-position-tooltip' => 'Fiscal positions are used to adapt taxes and accounts based on the customer location.',
                            'cash-rounding'           => 'Cash Rounding Method',
                            'cash-rounding-tooltip'   => 'Specifies the smallest cash-payable unit of the currency.',
                            'payment-method'          => 'Payment Method',
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
            'created-by'       => 'Created By',
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

        'summarizers' => [
            'total' => 'Total',
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
            'payment-reference'            => 'Payment Reference',
            'narration'                    => 'Narration',
            'partner'                      => 'Partner',
            'journal'                      => 'Journal',
            'fiscal-position'              => 'Fiscal Position',
            'currency'                     => 'Currency',
            'company'                      => 'Company',
            'date'                         => 'Accounting Date',
            'delivery-date'                => 'Delivery Date',
            'amount-untaxed'               => 'Untaxed Amount',
            'amount-tax'                   => 'Tax Amount',
            'amount-total'                 => 'Total Amount',
            'amount-residual'              => 'Amount Due',
            'checked'                      => 'Checked',
            'posted-before'                => 'Posted Before',
            'is-move-sent'                 => 'Sent',
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

            'journal-items' => [
                'title' => 'Journal Items',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Account',
                        'partner'  => 'Partner',
                        'label'    => 'Label',
                        'currency' => 'Currency',
                        'due-date' => 'Due Date',
                        'taxes'    => 'Taxes',
                        'debit'    => 'Debit',
                        'credit'   => 'Credit',
                    ],
                ],
            ],
        ],
    ],

];
