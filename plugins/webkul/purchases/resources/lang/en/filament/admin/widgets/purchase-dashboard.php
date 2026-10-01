<?php

return [
    'unknown'      => 'Unknown',
    'other'        => 'Other',
    'share'        => 'Share',
    'status'       => 'Status',
    'stats'        => [
        'heading'              => 'Key Metrics',
        'draft-rfqs'           => 'Draft RFQs',
        'sent-rfqs'            => 'Sent RFQs',
        'confirmed-orders'     => 'Confirmed Orders',
        'total-purchase-value' => 'Total Purchase Value',
        'increase'             => ':percent% increase',
        'decrease'             => ':percent% decrease',
        'no-change'            => 'No change',
    ],

    'trend'        => [
        'heading' => 'Purchase Orders Trend by State',
        'states'  => [
            'draft'    => 'Draft',
            'sent'     => 'Sent',
            'purchase' => 'Purchase',
            'done'     => 'Done',
            'canceled' => 'Canceled',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Vendor Spend Share',
        'total-purchased' => 'Total Purchased',
    ],

    'top-orders'   => [
        'heading' => 'Top Orders',
        'columns' => [
            'reference'  => 'Order Number',
            'vendor'     => 'Vendor',
            'ordered-at' => 'Ordered At',
            'amount'     => 'Total Amount',
        ],
    ],

    'top-products' => [
        'heading' => 'Top Purchased Products',
        'columns' => [
            'name'     => 'Product',
            'quantity' => 'Total Quantity',
            'value'    => 'Total Value',
        ],
    ],
];
