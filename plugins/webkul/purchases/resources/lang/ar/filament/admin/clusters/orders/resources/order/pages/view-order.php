<?php

return [
    'header-actions' => [
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
