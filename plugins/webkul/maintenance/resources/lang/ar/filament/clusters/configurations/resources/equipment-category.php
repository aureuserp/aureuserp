<?php

return [
    'navigation' => [
        'title' => 'الفئات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',

                'fields' => [
                    'name'       => 'الاسم',
                    'technician' => 'المسؤول',
                    'company'    => 'الشركة',
                    'note'       => 'ملاحظة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'technician' => 'المسؤول',
            'company'    => 'الشركة',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'technician' => 'المسؤول',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفئة',
                    'body'  => 'تم تحديث الفئة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئة',
                    'body'  => 'تم حذف الفئة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئات',
                    'body'  => 'تم حذف الفئات بنجاح.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الفئة',
                    'body'  => 'تم إنشاء الفئة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',

                'entries' => [
                    'name'       => 'الاسم',
                    'technician' => 'المسؤول',
                    'company'    => 'الشركة',
                    'note'       => 'ملاحظة',
                ],
            ],
        ],
    ],
];
