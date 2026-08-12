<?php

return [
    'notification' => [
        'title' => 'تم تحديث الدفعة',
        'body'  => 'تم تحديث الدفعة بنجاح.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'طباعة',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الدفعة',
                    'body'  => 'تم حذف الدفعة بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الدفعة',
                    'body'  => 'لا يمكن حذف الدفعة لأنها قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
