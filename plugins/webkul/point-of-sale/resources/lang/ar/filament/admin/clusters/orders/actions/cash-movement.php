<?php

return [
    'label'        => 'إيداع/سحب نقدي',

    'form' => [
        'fields' => [
            'type'   => 'النوع',
            'amount' => 'المبلغ',
            'reason' => 'السبب',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'تم تسجيل الحركة النقدية',
            'body'  => 'تم تحديث رصيد درج النقد.',
        ],
    ],
];
