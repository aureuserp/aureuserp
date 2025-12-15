<?php

return [
    'title' => 'الأيام الإلزامية',

    'model-label' => 'يوم إلزامي',

    'navigation' => [
        'title' => 'العطل الإلزامية',
    ],

    'form' => [
        'fields' => [
            'name'       => 'الاسم',
            'start-date' => 'تاريخ البداية',
            'end-date'   => 'تاريخ النهاية',
            'color'      => 'اللون',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'company-name' => 'اسم الشركة',
            'created-by'   => 'أنشئ بواسطة',
            'start-date'   => 'تاريخ البداية',
            'end-date'     => 'تاريخ النهاية',
        ],

        'filters' => [
            'name'         => 'الاسم',
            'company-name' => 'اسم الشركة',
            'created-by'   => 'أنشئ بواسطة',
            'start-date'   => 'تاريخ البداية',
            'end-date'     => 'تاريخ النهاية',
        ],

        'groups' => [
            'name'         => 'الاسم',
            'company-name' => 'اسم الشركة',
            'created-by'   => 'أنشئ بواسطة',
            'start-date'   => 'تاريخ البداية',
            'end-date'     => 'تاريخ النهاية',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث اليوم الإلزامي',
                    'body'  => 'تم تحديث اليوم الإلزامي بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف اليوم الإلزامي',
                    'body'  => 'تم حذف اليوم الإلزامي بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الأيام الإلزامية',
                    'body'  => 'تم حذف الأيام الإلزامية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'الاسم',
            'start-date' => 'تاريخ البداية',
            'end-date'   => 'تاريخ النهاية',
            'color'      => 'اللون',
        ],
    ],
];
