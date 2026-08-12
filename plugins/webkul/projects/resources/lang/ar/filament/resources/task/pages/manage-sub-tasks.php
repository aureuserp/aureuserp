<?php

return [
    'title' => 'المهام الفرعية',

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'إضافة مهمة فرعية',

                'notification' => [
                    'title' => 'تم إنشاء المهمة',
                    'body'  => 'تم إنشاء المهمة بنجاح.',
                ],
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المهمة',
                    'body'  => 'تم استعادة المهمة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المهمة',
                    'body'  => 'تم حذف المهمة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المهمة نهائياً',
                    'body'  => 'تم حذف المهمة نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
