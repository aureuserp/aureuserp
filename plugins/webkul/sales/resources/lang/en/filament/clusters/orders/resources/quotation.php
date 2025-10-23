<?php

return [
    'title' => 'Quotation',

    'navigation' => [
        'title' => 'Quotations',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'customer'       => 'Customer',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Quotation Date',
                    'order-date'     => 'Order Date',
                    'payment-term'   => 'Payment Term',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Order Line',

                'repeater' => [
                    'products' => [
                        'title'       => 'Products',
                        'add-product' => 'Add Product',

                        'columns'     => [
                            'product'             => 'Product',
                            'product-variants'    => 'Product Variants',
                            'product-simple'      => 'Product Simple',
                            'quantity'            => 'Quantity',
                            'uom'                 => 'UOM',
                            'lead-time'           => 'Lead Time',
                            'qty-delivered'       => 'Quantity Delivered',
                            'qty-invoiced'        => 'Quantity Invoiced',
                            'packaging-qty'       => 'Packaging Quantity',
                            'packaging'           => 'Packaging',
                            'unit-price'          => 'Unit Price',
                            'cost'                => 'Cost',
                            'margin'              => 'Margin',
                            'taxes'               => 'Taxes',
                            'amount'              => 'Amount',
                            'margin-percentage'   => 'Margin (%)',
                            'discount-percentage' => 'Discount (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Product',
                            'product-variants'    => 'Product Variants',
                            'product-simple'      => 'Product Simple',
                            'quantity'            => 'Quantity',
                            'uom'                 => 'Unit Of Measure',
                            'lead-time'           => 'Lead Time',
                            'qty-delivered'       => 'Quantity Delivered',
                            'qty-invoiced'        => 'Quantity Invoiced',
                            'packaging-qty'       => 'Packaging Quantity',
                            'packaging'           => 'Packaging',
                            'unit-price'          => 'Unit Price',
                            'cost'                => 'Cost',
                            'margin'              => 'Margin',
                            'taxes'               => 'Taxes',
                            'amount'              => 'Amount',
                            'margin-percentage'   => 'Margin (%)',
                            'discount-percentage' => 'Discount (%)',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Optional Products',
                        'add-product' => 'Add Product',

                        'columns' => [
                            'product'             => 'Product',
                            'description'         => 'Description',
                            'quantity'            => 'Quantity',
                            'uom'                 => 'Unit Of Measure',
                            'unit-price'          => 'Unit Price',
                            'discount-percentage' => 'Discount (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Product',
                            'description'         => 'Description',
                            'quantity'            => 'Quantity',
                            'uom'                 => 'Unit Of Measure',
                            'unit-price'          => 'Unit Price',
                            'discount-percentage' => 'Discount (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Add Order Line',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Product added',
                                        'body'  => 'The product has been added successfully.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Product Not Found',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Product Already Exists',
                                        'body'  => 'This product is already in the order lines. Please update the existing line instead.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Missing Product Data',
                                        'body'  => 'Unable to process the selected product.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Other Information',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Sales',

                        'fields' => [
                            'sales-person'       => 'Sales Person',
                            'customer-reference' => 'Customer Reference',
                            'tags'               => 'Tags',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Shipping',
                        'fields' => [
                            'commitment-date' => 'Delivery Date',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Tracking',
                        'fields' => [
                            'source-document' => 'Source Document',
                            'medium'          => 'Medium',
                            'source'          => 'Source',
                            'campaign'        => 'Campaign',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Additional Information',

                        'fields' => [
                            'company'  => 'Company',
                            'currency' => 'Currency',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Number',
            'status'             => 'Status',
            'invoice-status'     => 'Invoice Status',
            'creation-date'      => 'Creation Date',
            'commitment-date'    => 'Commitment Date',
            'expected-date'      => 'Expected Date',
            'customer'           => 'Customer',
            'sales-person'       => 'Sales Person',
            'sales-team'         => 'Sales Team',
            'untaxed-amount'     => 'Untaxed Amount',
            'amount-tax'         => 'Amount Tax',
            'amount-total'       => 'Amount Total',
            'customer-reference' => 'Customer Reference',
        ],

        'filters' => [
            'sales-person'     => 'Sales Person',
            'utm-source'       => 'UTM Source',
            'company'          => 'Company',
            'customer'         => 'Customer',
            'journal'          => 'Journal',
            'invoice-address'  => 'Invoice Address',
            'shipping-address' => 'Shipping Address',
            'fiscal-position'  => 'Fiscal Position',
            'payment-term'     => 'Payment Term',
            'currency'         => 'Currency',
            'created-at'       => 'Created At',
            'updated-at'       => 'Updated At',
        ],

        'groups' => [
            'medium'          => 'Medium',
            'source'          => 'Source',
            'team'            => 'Team',
            'sales-person'    => 'Sales Person',
            'currency'        => 'Currency',
            'company'         => 'Company',
            'customer'        => 'Customer',
            'quotation-date'  => 'Quotation Date',
            'commitment-date' => 'Commitment Date',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Quotation restored',
                    'body'  => 'The quotation has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Quotation deleted',
                    'body'  => 'The quotation has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Quotation force deleted',
                    'body'  => 'The quotation has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Quotations restored',
                    'body'  => 'The quotations has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Quotations deleted',
                    'body'  => 'The quotations has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Quotations force deleted',
                    'body'  => 'The quotations has been force deleted successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Quotations created',
                    'body'  => 'The quotations has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'sale-order'     => 'Sales Order',
                    'customer'       => 'Customer',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Quotation Date',
                    'payment-term'   => 'Payment Term',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Order Line',

                'repeater' => [
                    'products' => [
                        'title'       => 'Products',
                        'add-product' => 'Add Product',
                        'entries'     => [
                            'product'             => 'Product',
                            'product-variants'    => 'Product Variants',
                            'product-simple'      => 'Product Simple',
                            'quantity'            => 'Quantity',
                            'uom'                 => 'Unit Of Measure',
                            'lead-time'           => 'Lead Time',
                            'packaging-qty'       => 'Packaging Quantity',
                            'packaging'           => 'Packaging',
                            'unit-price'          => 'Unit Price',
                            'cost'                => 'Cost',
                            'margin'              => 'Margin',
                            'taxes'               => 'Taxes',
                            'amount'              => 'Amount',
                            'margin-percentage'   => 'Margin (%)',
                            'discount-percentage' => 'Discount (%)',
                            'sub-total'           => 'Sub Total',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Optional Products',
                        'add-product' => 'Add Product',
                        'entries'     => [
                            'product'             => 'Product',
                            'description'         => 'Description',
                            'quantity'            => 'Quantity',
                            'uom'                 => 'Unit Of Measure',
                            'unit-price'          => 'Unit Price',
                            'discount-percentage' => 'Discount (%)',
                            'sub-total'           => 'Sub Total',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Add Order Line',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Product added',
                                        'body'  => 'The product has been added successfully.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Other Information',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Sales',

                        'entries' => [
                            'sales-person'       => 'Sales Person',
                            'customer-reference' => 'Customer Reference',
                            'tags'               => 'Tags',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Shipping',
                        'entries' => [
                            'commitment-date' => 'Delivery Date',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Tracking',
                        'entries' => [
                            'source-document' => 'Source Document',
                            'medium'          => 'Medium',
                            'source'          => 'Source',
                            'campaign'        => 'Campaign',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Additional Information',

                        'entries' => [
                            'company'  => 'Company',
                            'currency' => 'Currency',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
    ],
];
