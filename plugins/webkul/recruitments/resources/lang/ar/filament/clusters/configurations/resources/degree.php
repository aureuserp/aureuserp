<?php

return [
    'title' => 'الدرجات العلمية',

    'navigation' => [
        'title' => 'الدرجات العلمية',
        'group' => 'الطلبات',
    ],

    'groups' => [
        'status'     => 'الحالة',
        'created-by' => 'أنشئ بواسطة',
        'created-at' => 'تاريخ الإنشاء',
        'updated-at' => 'تاريخ التحديث',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'name-placeholder' => 'أدخل اسم الدرجة العلمية',
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
                    'title' => 'تم تحديث الدرجة العلمية',
                    'body'  => 'تم تحديث الدرجة العلمية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الدرجة العلمية',
                    'body'  => 'تم حذف الدرجة العلمية بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الدرجات العلمية',
                    'body'  => 'تم حذف الدرجات العلمية بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الدرجة العلمية',
                    'body'  => 'تم إنشاء الدرجة العلمية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'الاسم',
    ],
];
