<?php

return [
    'title' => 'سجلات الوقت',

    'navigation' => [
        'title' => 'سجلات الوقت',
        'group' => 'المشروع',
    ],

    'form' => [
        'date'                   => 'التاريخ',
        'employee'               => 'الموظف',
        'project'                => 'المشروع',
        'task'                   => 'المهمة',
        'description'            => 'الوصف',
        'time-spent'             => 'الوقت المستغرق',
        'time-spent-helper-text' => 'الوقت المستغرق بالساعات (مثال: 1.5 ساعة تعني ساعة و30 دقيقة)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'التاريخ',
            'employee'    => 'الموظف',
            'project'     => 'المشروع',
            'task'        => 'المهمة',
            'description' => 'الوصف',
            'time-spent'  => 'الوقت المستغرق',
            'created-at'  => 'تاريخ الإنشاء',
            'updated-at'  => 'تاريخ التحديث',
        ],

        'groups' => [
            'date'       => 'التاريخ',
            'employee'   => 'الموظف',
            'project'    => 'المشروع',
            'task'       => 'المهمة',
            'creator'    => 'المُنشئ',
        ],

        'filters' => [
            'date-from'  => 'من تاريخ',
            'date-until' => 'حتى تاريخ',
            'employee'   => 'الموظف',
            'project'    => 'المشروع',
            'task'       => 'المهمة',
            'creator'    => 'المُنشئ',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث سجل الوقت',
                    'body'  => 'تم تحديث سجل الوقت بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف سجل الوقت',
                    'body'  => 'تم حذف سجل الوقت بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف سجلات الوقت',
                    'body'  => 'تم حذف سجلات الوقت بنجاح.',
                ],
            ],
        ],
    ],
];
