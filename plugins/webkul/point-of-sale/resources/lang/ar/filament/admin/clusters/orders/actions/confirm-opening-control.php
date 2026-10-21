<?php

return [
    'label'        => 'فتح جلسة',

    'form' => [
        'fields' => [
            'cash-balance-start' => 'الرصيد النقدي الافتتاحي',
            'opening-notes'      => 'ملاحظات',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'تم فتح الجلسة',
            'body'  => 'الجلسة قيد التنفيذ الآن.',
        ],
    ],
];
