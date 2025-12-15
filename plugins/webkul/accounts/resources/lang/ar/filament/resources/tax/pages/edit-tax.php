<?php

return [
    'notification' => [
        'title' => 'تم تحديث الضريبة',
        'body'  => 'تم تحديث الضريبة بنجاح.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الضريبة',
                    'body'  => 'تم حذف الضريبة بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الضريبة',
                    'body'  => 'لا يمكن حذف الضريبة لأنها قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
