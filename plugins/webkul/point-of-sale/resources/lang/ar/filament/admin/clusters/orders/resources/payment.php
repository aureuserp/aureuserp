<?php

return [
    'navigation' => [
        'title' => 'الدفعات',
        'group' => 'نقطة البيع',
    ],

    'table' => [
        'columns' => [
            'order'           => 'طلب',
            'session'         => 'جلسة',
            'payment-method'  => 'الطريقة',
            'amount'          => 'المبلغ',
            'partner'         => 'العميل',
            'is-change'       => 'الباقي',
            'terminal-status' => 'حالة الطرفية',
            'paid-at'         => 'تاريخ الدفع',
        ],

        'groups' => [
            'payment-method' => 'الطريقة',
            'session'        => 'جلسة',
        ],

        'filters' => [
            'payment-method' => 'الطريقة',
            'session'        => 'جلسة',
        ],
    ],
];
