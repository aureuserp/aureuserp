<?php

return [
    'notification' => [
        'title' => 'تم تحديث الاستلام',
        'body'  => 'تم تحديث الاستلام بنجاح.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'طباعة',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الاستلام',
                    'body'  => 'تم حذف الاستلام بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الاستلام',
                    'body'  => 'لا يمكن حذف الاستلام لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
