<?php

return [
    'title' => 'المراحل',

    'navigation' => [
        'title' => 'المراحل',
        'group' => 'المناصب الوظيفية',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'المعلومات العامة',

                'fields' => [
                    'stage-name'   => 'اسم المرحلة',
                    'sort'         => 'ترتيب التسلسل',
                    'requirements' => 'المتطلبات',
                ],
            ],

            'tooltips' => [
                'title'       => 'التلميحات',
                'description' => 'تحديد التسمية المخصصة لحالة الطلب.',

                'fields' => [
                    'gray-label'          => 'التسمية الرمادية',
                    'gray-label-tooltip'  => 'التسمية للحالة الرمادية.',
                    'red-label'           => 'التسمية الحمراء',
                    'red-label-tooltip'   => 'التسمية للحالة الحمراء.',
                    'green-label'         => 'التسمية الخضراء',
                    'green-label-tooltip' => 'التسمية للحالة الخضراء.',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'fields' => [
                    'job-positions' => 'المناصب الوظيفية',
                    'folded'        => 'مطوي',
                    'hired-stage'   => 'مرحلة التوظيف',
                    'default-stage' => 'المرحلة الافتراضية',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'المعرف',
            'name'               => 'اسم المرحلة',
            'hired-stage'        => 'مرحلة التوظيف',
            'default-stage'      => 'المرحلة الافتراضية',
            'folded'             => 'مطوي',
            'job-positions'      => 'المناصب الوظيفية',
            'created-by'         => 'أنشئ بواسطة',
            'created-at'         => 'تاريخ الإنشاء',
            'updated-at'         => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'         => 'اسم المرحلة',
            'job-position' => 'المنصب الوظيفي',
            'folded'       => 'مطوي',
            'gray-label'   => 'التسمية الرمادية',
            'red-label'    => 'التسمية الحمراء',
            'green-label'  => 'التسمية الخضراء',
            'created-by'   => 'أنشئ بواسطة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'job-position' => 'المنصب الوظيفي',
            'stage-name'   => 'اسم المرحلة',
            'folded'       => 'مطوي',
            'gray-label'   => 'التسمية الرمادية',
            'red-label'    => 'التسمية الحمراء',
            'green-label'  => 'التسمية الخضراء',
            'created-by'   => 'أنشئ بواسطة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المراحل',
                        'body'  => 'تم حذف المراحل بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المراحل',
                        'body'  => 'لا يمكن حذف المراحل لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المراحل',
                    'body'  => 'تم حذف المراحل بنجاح.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'مرحلة جديدة',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'المعلومات العامة',

                'entries' => [
                    'stage-name'   => 'اسم المرحلة',
                    'sort'         => 'ترتيب التسلسل',
                    'requirements' => 'المتطلبات',
                ],
            ],

            'tooltips' => [
                'title'       => 'التلميحات',
                'description' => 'تحديد التسمية المخصصة لحالة الطلب.',

                'entries' => [
                    'gray-label'          => 'التسمية الرمادية',
                    'gray-label-tooltip'  => 'التسمية للحالة الرمادية.',
                    'red-label'           => 'التسمية الحمراء',
                    'red-label-tooltip'   => 'التسمية للحالة الحمراء.',
                    'green-label'         => 'التسمية الخضراء',
                    'green-label-tooltip' => 'التسمية للحالة الخضراء.',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'entries' => [
                    'job-positions'      => 'المنصب الوظيفي',
                    'folded'             => 'مطوي',
                    'hired-stage'        => 'مرحلة التوظيف',
                    'default-stage'      => 'المرحلة الافتراضية',
                ],
            ],
        ],
    ],

];
