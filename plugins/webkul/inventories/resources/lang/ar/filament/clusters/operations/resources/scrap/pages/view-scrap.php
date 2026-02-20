<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الخردة',
                    'body'  => 'تم حذف الخردة بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الخردة',
                    'body'  => 'لا يمكن حذف الخردة لأنها قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
