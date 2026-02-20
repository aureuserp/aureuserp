<?php

return [
    'navigation' => [
        'title' => 'المناصب الوظيفية',
        'group' => 'المناصب الوظيفية',
    ],

    'global-search' => [
        'name'            => 'المنصب الوظيفي',
        'department'      => 'القسم',
        'employment-type' => 'نوع التوظيف',
        'company'         => 'الشركة',
        'created-by'      => 'أنشئ بواسطة',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'معلومات التوظيف',

                'fields' => [
                    'job-position-title'         => 'مسمى المنصب الوظيفي',
                    'job-position-title-tooltip' => 'أدخل المسمى الوظيفي الرسمي',
                    'department'                 => 'القسم',
                    'department-modal-title'     => 'إنشاء قسم',
                    'job-location'               => 'موقع العمل',
                    'industry'                   => 'القطاع',
                    'company'                    => 'الشركة',
                    'employment-type'            => 'نوع التوظيف',
                    'interviewers'               => 'المحاورون',
                    'recruiter'                  => 'المسؤول عن التوظيف',
                    'manager'                    => 'المدير',
                ],
            ],

            'job-description' => [
                'title' => 'الوصف الوظيفي',

                'fields' => [
                    'job-description'  => 'الوصف الوظيفي',
                    'job-requirements' => 'متطلبات الوظيفة',
                ],
            ],

            'workforce-planning' => [
                'title' => 'تخطيط القوى العاملة',

                'fields' => [
                    'recruitment-target' => 'هدف التوظيف',
                    'date-from'          => 'من تاريخ',
                    'date-to'            => 'إلى تاريخ',
                    'expected-skills'    => 'المهارات المتوقعة',
                    'employment-type'    => 'نوع التوظيف',
                    'status'             => 'الحالة',
                ],
            ],

            'position-status' => [
                'title' => 'حالة المنصب',

                'fields' => [
                    'status' => 'الحالة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'المعرف',
            'name'               => 'المنصب الوظيفي',
            'department'         => 'القسم',
            'job-position'       => 'المنصب الوظيفي',
            'company'            => 'الشركة',
            'expected-employees' => 'الموظفون المتوقعون',
            'current-employees'  => 'الموظفون الحاليون',
            'status'             => 'الحالة',
            'created-by'         => 'أنشئ بواسطة',
            'created-at'         => 'تاريخ الإنشاء',
            'updated-at'         => 'تاريخ التحديث',
        ],

        'filters' => [
            'department'      => 'القسم',
            'employment-type' => 'نوع التوظيف',
            'job-position'    => 'المنصب الوظيفي',
            'company'         => 'الشركة',
            'status'          => 'الحالة',
            'created-by'      => 'أنشئ بواسطة',
            'updated-at'      => 'تاريخ التحديث',
            'created-at'      => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'job-position'    => 'المنصب الوظيفي',
            'company'         => 'الشركة',
            'department'      => 'القسم',
            'employment-type' => 'نوع التوظيف',
            'created-by'      => 'أنشئ بواسطة',
            'created-at'      => 'تاريخ الإنشاء',
            'updated-at'      => 'تاريخ التحديث',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المنصب الوظيفي',
                    'body'  => 'تم استعادة المنصب الوظيفي بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المنصب الوظيفي',
                    'body'  => 'تم حذف المنصب الوظيفي بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المناصب الوظيفية',
                    'body'  => 'تم استعادة المناصب الوظيفية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المناصب الوظيفية',
                    'body'  => 'تم حذف المناصب الوظيفية بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المناصب الوظيفية نهائياً',
                    'body'  => 'تم حذف المناصب الوظيفية نهائياً بنجاح.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'المناصب الوظيفية',
                    'body'  => 'تم إنشاء المناصب الوظيفية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'معلومات التوظيف',

                'entries' => [
                    'job-position-title' => 'مسمى المنصب الوظيفي',
                    'department'         => 'القسم',
                    'company'            => 'الشركة',
                    'employment-type'    => 'نوع التوظيف',
                    'job-location'       => 'موقع العمل',
                    'industry'           => 'القطاع',
                    'manager'            => 'المدير',
                    'recruiter'          => 'المسؤول عن التوظيف',
                    'interviewers'       => 'المحاورون',
                ],
            ],
            'job-description' => [
                'title' => 'الوصف الوظيفي',

                'entries' => [
                    'job-description'  => 'الوصف الوظيفي',
                    'job-requirements' => 'متطلبات الوظيفة',
                ],
            ],
            'work-planning' => [
                'title' => 'تخطيط القوى العاملة',

                'entries' => [
                    'expected-employees' => 'الموظفون المتوقعون',
                    'current-employees'  => 'الموظفون الحاليون',
                    'date-from'          => 'من تاريخ',
                    'date-to'            => 'إلى تاريخ',
                    'recruitment-target' => 'هدف التوظيف',
                ],
            ],
            'position-status' => [
                'title' => 'حالة المنصب',

                'entries' => [
                    'status' => 'الحالة',
                ],
            ],
        ],
    ],
];
