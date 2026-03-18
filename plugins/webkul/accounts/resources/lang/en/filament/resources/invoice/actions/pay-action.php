<?php

return [
    'title' => 'Pay',

    'form' => [
        'fields' => [
            'journal'              => 'Journal',
            'amount'               => 'Amount',
            'currency'             => 'Currency',
            'payment-method-line'  => 'Payment Method Line',
            'payment-date'         => 'Payment Date',
            'partner-bank-account' => 'Partner Bank Account',
            'communication'        => 'Memo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Payment Failed',
        ],
    ],
];
