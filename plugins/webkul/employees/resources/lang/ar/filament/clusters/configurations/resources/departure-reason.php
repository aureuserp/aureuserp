<?php

return [
    'title' => 'أسباب المغادرة',

    'navigation' => [
        'title' => 'أسباب المغادرة',
        'group' => 'الموظف',
    ],

    'groups' => [
        'status'     => 'الحالة',
        'created-by' => 'أنشئ بواسطة',
        'created-at' => 'تاريخ الإنشاء',
        'updated-at' => 'تاريخ التحديث',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'الاسم',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'       => 'الاسم',
            'employee'   => 'الموظف',
            'created-by' => 'أنشئ بواسطة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث سبب المغادرة',
                    'body'  => 'تم تحديث سبب المغادرة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف سبب المغادرة',
                    'body'  => 'تم حذف سبب المغادرة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف أسباب المغادرة',
                    'body'  => 'تم حذف أسباب المغادرة بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء سبب المغادرة',
                    'body'  => 'تم إنشاء سبب المغادرة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'الاسم',
    ],
];
