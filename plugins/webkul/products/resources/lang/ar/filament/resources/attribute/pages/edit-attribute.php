<?php

return [
    'notification' => [
        'title' => 'تم تحديث السمة',
        'body'  => 'تم تحديث السمة بنجاح.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف السمة',
                    'body'  => 'تم حذف السمة بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف السمة',
                    'body'  => 'لا يمكن حذف السمة لأنها قيد الاستخدام حالياً من قبل المنتج: :products',
                ],
            ],
        ],
    ],
];
