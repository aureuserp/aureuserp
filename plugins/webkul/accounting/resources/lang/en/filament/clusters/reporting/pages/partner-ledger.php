<?php

return [
    'filters' => [
        'ranges' => [
            'today'        => 'Today',
            'yesterday'    => 'Yesterday',
            'this-month'   => 'This Month',
            'last-month'   => 'Last Month',
            'this-quarter' => 'This Quarter',
            'last-quarter' => 'Last Quarter',
            'this-year'    => 'This Year',
            'last-year'    => 'Last Year',
        ],
    ],

    'table' => [
        'total' => 'Total Partner Ledger',
        'empty' => 'No partners with transactions in this period',
    ],

    'pdf' => [
        'period'       => 'From :from to :to',
        'currency'     => 'Amounts in :currency',
        'page'         => 'Page',
        'generated-on' => 'Generated on :date',
    ],
];
