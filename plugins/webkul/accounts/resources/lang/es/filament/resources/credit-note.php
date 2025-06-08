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
                    'customer-invoice' => 'Customer Credit Note',
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

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'customer-invoice' => 'Customer Credit Note',
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
