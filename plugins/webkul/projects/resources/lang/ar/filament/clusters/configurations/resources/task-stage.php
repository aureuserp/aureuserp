<?php

return [
    'navigation' => [
        'title' => 'مراحل المهام',
    ],

    'form' => [
        'name'    => 'الاسم',
        'project' => 'المشروع',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'project'    => 'المشروع',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'project'    => 'المشروع',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'project' => 'المشروع',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث مرحلة المهمة',
                    'body'  => 'تم تحديث مرحلة المهمة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مرحلة المهمة',
                    'body'  => 'تم استعادة مرحلة المهمة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مرحلة المهمة',
                    'body'  => 'تم حذف مرحلة المهمة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مرحلة المهمة نهائياً',
                        'body'  => 'تم حذف مرحلة المهمة نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف مرحلة المهمة',
                        'body'  => 'لا يمكن حذف مرحلة المهمة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مراحل المهام',
                    'body'  => 'تم استعادة مراحل المهام بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مراحل المهام',
                    'body'  => 'تم حذف مراحل المهام بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف مراحل المهام نهائياً',
                    'body'  => 'تم حذف مراحل المهام نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
