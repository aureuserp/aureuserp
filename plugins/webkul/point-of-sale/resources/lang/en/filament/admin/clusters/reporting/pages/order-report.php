<?php

return [
    'navigation' => [
        'title' => 'Orders',
    ],

    'title' => 'Order Analysis',

    'filters' => [
        'heading'       => 'Filters',
        'all-terminals' => 'All terminals',
        'day'           => 'By day',
        'week'          => 'By week',
        'month'         => 'By month',
    ],

    'table' => [
        'heading' => 'Orders',
        'total'   => 'Total',
        'empty'   => 'No orders in this period.',

        'columns' => [
            'period'  => 'Period',
            'orders'  => 'Orders',
            'untaxed' => 'Untaxed',
            'taxes'   => 'Taxes',
            'total'   => 'Total',
            'margin'  => 'Margin',
        ],
    ],

    'average' => 'Average order value: :amount',
];
