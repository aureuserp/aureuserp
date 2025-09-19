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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Location',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Secured',
                        'fields' => [
                            'payment-method' => 'Payment Method',
                            'auto-post'      => 'Auto Post',
                            'checked'        => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Additional Information',
                        'fields' => [
                            'company'  => 'Company',
                            'currency' => 'Currency',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Location',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Secured',
                        'entries' => [
                            'payment-method' => 'Payment Method',
                            'auto-post'      => 'Auto Post',
                            'checked'        => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Additional Information',
                        'entries' => [
                            'company'  => 'Company',
                            'currency' => 'Currency',
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
