<?php

return [
    'title'        => 'جلسة',
    'plural-title' => 'الجلسات',

    'log-attributes' => [
        'name'                  => 'جلسة',
        'state'                 => 'الحالة',
        'cash-balance-start'    => 'الرصيد الافتتاحي',
        'cash-balance-end-real' => 'الرصيد المعدود',
        'cash-difference'       => 'الفرق',
    ],

    'chatter' => [
        'opening' => [
            'difference' => 'فرق النقد عند الفتح: :amount',
            'expected'   => 'النقد المتوقع عند الفتح: :amount',
            'counted'    => 'النقد المعدود عند الفتح: :amount',
            'message'    => 'رسالة التحكم بالفتح: :message',
        ],

        'closing' => [
            'difference' => 'فرق الإغلاق: :amount',
            'expected'   => 'المتوقع عند الإغلاق: :amount',
            'counted'    => 'المعدود عند الإغلاق: :amount',
            'message'    => 'رسالة التحكم بالإغلاق: :message',
        ],

        'cash-movement' => [
            'in'     => 'إيداع نقدي: :amount',
            'out'    => 'سحب نقدي: :amount',
            'reason' => 'السبب: :reason',
        ],
    ],
];
