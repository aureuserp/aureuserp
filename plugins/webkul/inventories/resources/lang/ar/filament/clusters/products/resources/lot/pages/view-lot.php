<?php

return [
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
