<?php

return [
    'navigation' => [
        'title' => 'عرض قائمة أسعار المورد',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف سعر المورد',
                    'body'  => 'تم حذف سعر المورد بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف سعر المورد',
                    'body'  => 'لا يمكن حذف سعر المورد لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
