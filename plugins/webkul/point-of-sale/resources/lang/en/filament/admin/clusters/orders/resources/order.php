<?php

return [

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'order'      => 'Point of Sale Order',
                    'ordered-at' => 'Date',
                    'customer'   => 'Customer',
                    'cashier'    => 'Cashier',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title'   => 'Products',
                'columns' => [
                    'product'            => 'Product',
                    'lot'                => 'Lot/Serial Number',
                    'quantity'           => 'Quantity',
                    'uom'                => 'UoM',
                    'unit-price'         => 'Unit Price',
                    'discount'           => 'Disc.%',
                    'taxes'              => 'Taxes',
                    'tax-excluded'       => 'Tax Excl.',
                    'tax-included'       => 'Tax Incl.',
                    'full-product-name'  => 'Full Product Name',
                    'customer-note'      => 'Customer Note',
                    'total-cost'         => 'Total Cost',
                    'margin'             => 'Margin',
                    'margin-percent'     => 'Margin (%)',
                    'refunded-quantity'  => 'Refunded Quantity',
                ],

                'actions' => [
                    'open-product' => 'Open product',
                ],

                'summary' => [
                    'untaxed'  => 'Untaxed Amount',
                    'taxes'    => 'Taxes',
                    'rounding' => 'Rounding',
                    'total'    => 'Total',
                    'paid'     => 'Paid',
                    'change'   => 'Change',
                ],
            ],

            'payments' => [
                'title'  => 'Payments',
                'add'    => 'Add a line',
                'fields' => [
                    'paid-at'         => 'Date',
                    'method'          => 'Payment Method',
                    'amount'          => 'Amount',
                    'card-type'       => 'Card Number (Last 4 Digits)',
                    'card-brand'      => "Card's Brand",
                    'cardholder-name' => 'Card Owner Name',
                ],
            ],

            'extra-info' => [
                'title'  => 'Extra Info',
                'fields' => [
                    'receipt-number'  => 'Receipt Number',
                    'tracking-number' => 'Tracking Number',
                    'email'           => 'Email',
                    'mobile'          => 'Mobile',
                ],
            ],

            'notes' => [
                'title' => 'General Notes',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'order'         => 'Point of Sale Order',
                    'customer'      => 'Customer',
                    'session'       => 'Session',
                    'ordered-at'    => 'Ordered At',
                    'point-of-sale' => 'Point of Sale',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title'   => 'Order Line',
                'entries' => [
                    'product'    => 'Product',
                    'quantity'   => 'Quantity',
                    'unit-price' => 'Unit Price',
                    'taxes'      => 'Taxes',
                    'discount'   => 'Discount (%)',
                    'amount'     => 'Amount',
                ],
                'totals' => [
                    'untaxed' => 'Untaxed Amount',
                    'taxes'   => 'Taxes',
                    'total'   => 'Amount Total',
                    'margin'  => 'Margin',
                ],
            ],

            'payments' => [
                'title'   => 'Payments',
                'entries' => [
                    'method'  => 'Payment Method',
                    'amount'  => 'Amount',
                    'paid-at' => 'Paid At',
                ],
            ],

            'extra-info' => [
                'title'   => 'Extra Info',
                'entries' => [
                    'receipt-number'  => 'Receipt Number',
                    'tracking-number' => 'Tracking Number',
                    'email'           => 'Email',
                    'mobile'          => 'Mobile',
                ],
            ],

            'other-information' => [
                'title'   => 'Other Information',
                'entries' => [
                    'reference'      => 'Reference',
                    'receipt-number' => 'Receipt Number',
                    'operation'      => 'Operation',
                    'cashier'        => 'Cashier',
                    'paid'           => 'Paid',
                    'change'         => 'Change',
                ],
            ],
        ],
    ],
    'navigation' => [
        'title' => 'Orders',
        'group' => 'Point of Sale',
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Order Ref',
            'reference'            => 'Receipt Number',
            'session'              => 'Session',
            'config'               => 'Point of Sale',
            'partner'              => 'Customer',
            'ordered-at'           => 'Date',
            'amount-total'         => 'Total',
            'amount-paid'          => 'Paid',
            'user'                 => 'Cashier',
            'has-failed-operation' => 'Failed Operation',
            'is-invoiced'          => 'Invoiced',
            'is-edited'            => 'Edited',
            'tracking-number'      => 'Tracking Number',
            'state'                => 'Status',
        ],

        'groups' => [
            'session'    => 'Session',
            'config'     => 'Point of Sale',
            'state'      => 'Status',
            'ordered-at' => 'Ordered At',
        ],

        'filters' => [
            'state'   => 'Status',
            'session' => 'Session',
            'config'  => 'Point of Sale',
        ],
    ],
];
