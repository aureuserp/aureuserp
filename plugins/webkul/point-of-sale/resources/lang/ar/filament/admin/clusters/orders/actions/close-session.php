<?php

return [
    'label'        => 'إغلاق الجلسة',

    'form' => [
        'fields' => [
            'cash-balance-end-real'             => 'النقد المعدود',
            'cash-balance-end-real-helper-text' => 'الرصيد المتوقع: :expected',
            'closing-notes'                     => 'ملاحظات',
            'balancing-account'                 => 'حساب الموازنة',
            'balancing-account-helper-text'     => 'يُستخدم فقط عندما لا يتوازن قيد الإغلاق.',
        ],
    ],

    'notification' => [
        'unbalanced' => [
            'title' => 'قيد الإغلاق غير متوازن',
        ],

        'success' => [
            'title' => 'تم إغلاق الجلسة',
            'body'  => 'تم إغلاق الجلسة.',
        ],
    ],
];
