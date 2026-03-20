<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'نوع المهارة',
                'skill'       => 'المهارة',
                'skill-level' => 'مستوى المهارة',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'نوع المهارة',
            'skill'         => 'المهارة',
            'skill-level'   => 'مستوى المهارة',
            'level-percent' => 'نسبة المستوى',
            'created-by'    => 'أنشئ بواسطة',
            'user'          => 'المستخدم',
            'created-at'    => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'skill-type' => 'نوع المهارة',
        ],

        'header-actions' => [
            'add-skill' => 'إضافة مهارة',
        ],

        'filters' => [
            'activity-type'   => 'نوع النشاط',
            'activity-status' => 'حالة النشاط',
            'has-delay'       => 'يوجد تأخير',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المهارة',
                    'body'  => 'تم تحديث المهارة بنجاح.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء المهارة',
                    'body'  => 'تم إنشاء المهارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المهارة',
                    'body'  => 'تم حذف المهارة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المهارات',
                    'body'  => 'تم حذف المهارات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'نوع المهارة',
            'skill'         => 'المهارة',
            'skill-level'   => 'مستوى المهارة',
            'level-percent' => 'نسبة المستوى',
        ],
    ],
];
