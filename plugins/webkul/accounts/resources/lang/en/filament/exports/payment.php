<?php

return [
    'columns' => [
        'date'            => 'Date',
        'name'            => 'Name',
        'journal'         => 'Journal',
        'payment-method'  => 'Payment Method',
        'partner'         => 'Partner',
        'amount-currency' => 'Amount Currency',
        'amount'          => 'Amount',
        'state'           => 'State',
        'company'         => 'Company',
    ],

    'notification' => [
        'completed' => 'Your payment export has completed and :count row(s) exported.',
        'failed'    => ':count row(s) failed to export.',
    ],
];
