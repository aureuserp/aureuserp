<?php

return [
    'notification' => [
        'title' => 'تم تحديث الطلب',
        'body'  => 'تم تحديث الطلب بنجاح.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'تأكيد',
        ],

        'close' => [
            'label' => 'إغلاق',
        ],

        'cancel' => [
            'label' => 'إلغاء',
        ],

        'print' => [
            'label' => 'طباعة',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الطلب',
                    'body'  => 'تم حذف الطلب بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الطلب',
                    'body'  => 'لا يمكن حذف الطلب لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
