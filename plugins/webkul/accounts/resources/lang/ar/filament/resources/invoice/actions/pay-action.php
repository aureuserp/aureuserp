<?php

return [
    'title' => 'دفع',

    'form' => [
        'fields' => [
            'journal'              => 'دفتر اليومية',
            'amount'               => 'المبلغ',
            'currency'             => 'العملة',
            'payment-method-line'  => 'بند طريقة الدفع',
            'payment-date'         => 'تاريخ الدفع',
            'partner-bank-account' => 'الحساب البنكي للشريك',
            'communication'        => 'البيان',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'فشلت عملية الدفع',
        ],
    ],
];
