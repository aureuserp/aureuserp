<?php

return [
    'navigation' => [
        'title' => 'مراحل المشروع',
    ],

    'form' => [
        'name' => 'الاسم',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'name'         => 'الاسم',
            'is-completed' => 'مكتمل',
            'project'      => 'المشروع',
            'created-at'   => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-completed' => 'مكتمل',
            'project'      => 'المشروع',
            'creator'      => 'المُنشئ',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث مرحلة المشروع',
                    'body'  => 'تم تحديث مرحلة المشروع بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مرحلة المشروع',
                    'body'  => 'تم استعادة مرحلة المشروع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مرحلة المشروع',
                    'body'  => 'تم حذف مرحلة المشروع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مرحلة المشروع نهائياً',
                        'body'  => 'تم حذف مرحلة المشروع نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف مرحلة المشروع',
                        'body'  => 'لا يمكن حذف مرحلة المشروع لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مراحل المشروع',
                    'body'  => 'تم استعادة مراحل المشروع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مراحل المشروع',
                    'body'  => 'تم حذف مراحل المشروع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف مراحل المشروع نهائياً',
                    'body'  => 'تم حذف مراحل المشروع نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
