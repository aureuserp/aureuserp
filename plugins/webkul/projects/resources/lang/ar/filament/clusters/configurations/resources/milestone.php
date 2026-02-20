<?php

return [
    'navigation' => [
        'title' => 'المراحل الرئيسية',
    ],

    'form' => [
        'name'         => 'الاسم',
        'deadline'     => 'الموعد النهائي',
        'is-completed' => 'مكتمل',
        'project'      => 'المشروع',
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'deadline'     => 'الموعد النهائي',
            'is-completed' => 'مكتمل',
            'completed-at' => 'تاريخ الإكمال',
            'project'      => 'المشروع',
            'creator'      => 'المُنشئ',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
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
                    'title' => 'تم تحديث المرحلة الرئيسية',
                    'body'  => 'تم تحديث المرحلة الرئيسية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المرحلة الرئيسية',
                    'body'  => 'تم حذف المرحلة الرئيسية بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المراحل الرئيسية',
                    'body'  => 'تم حذف المراحل الرئيسية بنجاح.',
                ],
            ],
        ],
    ],
];
