<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'طباعة',

            'actions' => [
                'without-content' => [
                    'label' => 'طباعة الباركود',
                ],

                'with-content' => [
                    'label' => 'طباعة الباركود مع المحتوى',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الطرد',
                    'body'  => 'تم حذف الطرد بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الطرد',
                    'body'  => 'لا يمكن حذف الطرد لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
