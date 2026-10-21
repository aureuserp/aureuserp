<?php

return [
    'title' => 'Orders',

    'navigation' => [
        'label' => 'Orders',
    ],

    'walk-in' => 'Walk-in customer',

    'search' => 'Search by order, receipt or customer',

    'search-restaurant' => 'Search by order, receipt, customer or table',

    'select-order' => 'Select an order to see its lines.',

    'line-discount' => '−:discount% discount',

    'taxes' => 'Taxes',

    'total' => 'Total',

    'status' => [
        'active' => 'All active orders',
    ],

    'columns' => [
        'date'     => 'Date',
        'receipt'  => 'Receipt number',
        'order'    => 'Order number',
        'customer' => 'Customer',
        'cashier'  => 'Cashier',
        'tracking' => 'Tracking number',
        'table'    => 'Table',
        'total'    => 'Total',
        'status'   => 'Status',
    ],

    'refund' => [
        'prompt'    => 'Select the product(s) to refund and set the quantity',
        'refunded'  => 'Refunded:',
        'to-refund' => 'To refund:',
        'qty'       => 'Qty',
        'price'     => 'Price',
        'backspace' => 'Backspace',

        'max-exceeded' => [
            'title' => 'Maximum exceeded',
            'body'  => 'The requested quantity to be refunded is higher than the ordered quantity. :requested is requested while only :max can be refunded.',
        ],
    ],

    'actions' => [
        'print'    => 'Print receipt',
        'back'     => 'Back',
        'details'  => 'Details',
        'refund'   => 'Refund',
        'previous' => 'Previous page',
        'next'     => 'Next page',

        'refund-notification' => [
            'title' => 'Refund created',
            'body'  => 'Refund :order is ready to settle.',
        ],

        'cancel' => [
            'label'       => 'Cancel',
            'heading'     => 'Cancel this order?',
            'description' => 'The order is closed without payment and no longer blocks closing the register. This cannot be undone.',

            'notification' => [
                'title' => 'Order cancelled',
                'body'  => ':order was cancelled.',
            ],
        ],

        'invoice' => [
            'label'   => 'Invoice',
            'heading' => 'Create an invoice for this order?',

            'notification' => [
                'title' => 'Invoice created',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'No orders yet',
        'description' => 'Orders taken during the open session will appear here.',
    ],
];
