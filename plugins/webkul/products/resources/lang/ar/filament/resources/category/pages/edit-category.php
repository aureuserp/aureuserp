<?php

return [
    'notification' => [
        'title' => 'تم تحديث الفئة',
        'body'  => 'تم تحديث الفئة بنجاح.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الفئة',
                    'body'  => 'تم حذف الفئة بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الفئة',
                    'body'  => 'لا يمكن حذف الفئة لأنها قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'فشل تحديث الفئة',
            ],
        ],
    ],
];
