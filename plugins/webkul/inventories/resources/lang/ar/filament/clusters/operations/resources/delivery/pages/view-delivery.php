<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'طباعة',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف التسليم',
                    'body'  => 'تم حذف التسليم بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف التسليم',
                    'body'  => 'لا يمكن حذف التسليم لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
