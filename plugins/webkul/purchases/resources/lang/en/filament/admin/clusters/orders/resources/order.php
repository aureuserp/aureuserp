<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'vendor'                   => 'Vendor',
                    'vendor-reference'         => 'Vendor Reference',
                    'vendor-reference-tooltip' => 'The reference number of the sales order or bid provided by the vendor. It is used for matching when receiving products, as this reference is typically included in the vendor\'s delivery order.',
                    'agreement'                => 'Agreement',
                    'currency'                 => 'Currency',
                    'confirmation-date'        => 'Confirmation Date',
                    'order-deadline'           => 'Order Deadline',
                    'expected-arrival'         => 'Expected Arrival',
                    'confirmed-by-vendor'      => 'Confirmed by Vendor',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Products',

                'repeater' => [
                    'products' => [
                        'title'            => 'Products',
                        'add-product-line' => 'Add Product',

                        'fields' => [
                            'product'             => 'Product',
                            'expected-arrival'    => 'Expected Arrival',
                            'quantity'            => 'Quantity',
                            'received'            => 'Received',
                            'billed'              => 'Billed',
                            'unit'                => 'Unit',
                            'packaging-qty'       => 'Packaging Qty',
                            'packaging'           => 'Packaging',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount (%)',
                            'unit-price'          => 'Unit Price',
                            'amount'              => 'Amount',
                        ],

                        'columns' => [
                            'product'             => 'Product',
                            'expected-arrival'    => 'Expected Arrival',
                            'quantity'            => 'Quantity',
                            'received'            => 'Received',
                            'billed'              => 'Billed',
                            'unit'                => 'Unit',
                            'packaging-qty'       => 'Packaging Qty',
                            'packaging'           => 'Packaging',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount (%)',
                            'unit-price'          => 'Unit Price',
                            'amount'              => 'Amount',
                        ],
                    ],

                    'section' => [
                        'title' => 'Add Section',

                        'fields' => [
                        ],
                    ],

                    'note' => [
                        'title' => 'Add Note',

                        'fields' => [
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',

                'fields' => [
                    'buyer'             => 'Buyer',
                    'company'           => 'Company',
                    'source-document'   => 'Source Document',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'International Commercial Terms (Incoterms) are a set of standardized trade terms used in global transactions to define responsibilities between buyers and sellers.',
                    'incoterm-location' => 'Incoterm Location',
                    'payment-term'      => 'Payment Term',
                    'fiscal-position'   => 'Fiscal Position',
                ],
            ],

            'terms' => [
                'title' => 'Terms and Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'priority'         => 'Priority',
            'vendor-reference' => 'Vendor Reference',
            'reference'        => 'Reference',
            'vendor'           => 'Vendor',
            'buyer'            => 'Buyer',
            'company'          => 'Company',
            'order-deadline'   => 'Order Deadline',
            'source-document'  => 'Source Document',
            'untaxed-amount'   => 'Untaxed Amount',
            'total-amount'     => 'Total Amount',
            'status'           => 'Status',
            'billing-status'   => 'Billing Status',
            'currency'         => 'Currency',
            'billing-status'   => 'Billing Status',
        ],

        'groups' => [
            'vendor'     => 'Vendor',
            'buyer'      => 'Buyer',
            'state'      => 'State',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'status'           => 'Status',
            'vendor-reference' => 'Vendor Reference',
            'reference'        => 'Reference',
            'untaxed-amount'   => 'Untaxed Amount',
            'total-amount'     => 'Total Amount',
            'order-deadline'   => 'Order Deadline',
            'vendor'           => 'Vendor',
            'buyer'            => 'Buyer',
            'company'          => 'Company',
            'payment-term'     => 'Payment Term',
            'incoterm'         => 'Incoterm',
            'status'           => 'Status',
            'created-at'       => 'Created At',
            'updated-at'       => 'Updated At',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Order deleted',
                        'body'  => 'The order has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Order could not be deleted',
                        'body'  => 'The order cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Orders deleted',
                        'body'  => 'The orders has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Orders could not be deleted',
                        'body'  => 'The orders cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'vendor'                   => 'Vendor',
                    'vendor-reference'         => 'Vendor Reference',
                    'vendor-reference-tooltip' => 'The reference number of the sales order or bid provided by the vendor. It is used for matching when receiving products, as this reference is typically included in the vendor\'s delivery order.',
                    'agreement'                => 'Agreement',
                    'currency'                 => 'Currency',
                    'confirmation-date'        => 'Confirmation Date',
                    'order-deadline'           => 'Order Deadline',
                    'expected-arrival'         => 'Expected Arrival',
                    'confirmed-by-vendor'      => 'Confirmed by Vendor',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Products',

                'repeater' => [
                    'products' => [
                        'title'            => 'Products',
                        'add-product-line' => 'Add Product',

                        'entries' => [
                            'product'             => 'Product',
                            'expected-arrival'    => 'Expected Arrival',
                            'quantity'            => 'Quantity',
                            'received'            => 'Received',
                            'billed'              => 'Billed',
                            'unit'                => 'Unit',
                            'packaging-qty'       => 'Packaging Qty',
                            'packaging'           => 'Packaging',
                            'taxes'               => 'Taxes',
                            'discount-percentage' => 'Discount (%)',
                            'unit-price'          => 'Unit Price',
                            'amount'              => 'Amount',
                        ],
                    ],

                    'section' => [
                        'title' => 'Add Section',
                    ],

                    'note' => [
                        'title' => 'Add Note',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',

                'entries' => [
                    'buyer'             => 'Buyer',
                    'company'           => 'Company',
                    'source-document'   => 'Source Document',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'International Commercial Terms (Incoterms) are a set of standardized trade terms used in global transactions to define responsibilities between buyers and sellers.',
                    'incoterm-location' => 'Incoterm Location',
                    'payment-term'      => 'Payment Term',
                    'fiscal-position'   => 'Fiscal Position',
                ],
            ],

            'terms' => [
                'title' => 'Terms and Conditions',
            ],
        ],
    ],
];
