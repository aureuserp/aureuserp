<?php

return [
    'title' => 'المهام',

    'header-actions' => [
        'create' => [
            'label' => 'مهمة جديدة',
        ],
    ],

    'table' => [
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

    'tabs' => [
        'open-tasks'       => 'المهام المفتوحة',
        'my-tasks'         => 'مهامي',
        'unassigned-tasks' => 'المهام غير المسندة',
        'closed-tasks'     => 'المهام المغلقة',
        'starred-tasks'    => 'المهام المميزة',
        'archived-tasks'   => 'المهام المؤرشفة',
    ],
];
