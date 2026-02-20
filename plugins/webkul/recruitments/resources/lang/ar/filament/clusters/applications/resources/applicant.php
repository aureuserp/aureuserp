<?php

return [
    'title' => 'المتقدم',

    'navigation' => [
        'title' => 'المتقدمون',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'معلومات عامة',

                'fields' => [
                    'evaluation-good'           => 'التقييم: جيد',
                    'evaluation-very-good'      => 'التقييم: جيد جداً',
                    'evaluation-very-excellent' => 'التقييم: ممتاز',
                    'hired'                     => 'تم التوظيف',
                    'candidate-name'            => 'اسم المرشح',
                    'email'                     => 'البريد الإلكتروني',
                    'phone'                     => 'الهاتف',
                    'linkedin-profile'          => 'ملف LinkedIn',
                    'recruiter'                 => 'المُوظِّف',
                    'interviewer'               => 'المُقابِل',
                    'tags'                      => 'الوسوم',
                    'notes'                     => 'ملاحظات',
                    'hired-date'                => 'تاريخ التوظيف',
                    'job-position'              => 'المسمى الوظيفي',
                ],
            ],

            'education-and-availability' => [
                'title' => 'التعليم والتوفر',

                'fields' => [
                    'degree'            => 'الدرجة العلمية',
                    'availability-date' => 'تاريخ التوفر',
                ],
            ],

            'department' => [
                'title' => 'القسم',
            ],

            'salary' => [
                'title' => 'الراتب المتوقع والمقترح',

                'fields' => [
                    'expected-salary'       => 'الراتب المتوقع',
                    'salary-proposed-extra' => 'مزايا أخرى',
                    'proposed-salary'       => 'الراتب المقترح',
                    'salary-expected-extra' => 'مزايا أخرى',
                ],
            ],

            'source-and-medium' => [
                'title' => 'المصدر والوسيط',

                'fields' => [
                    'source' => 'المصدر',
                    'medium' => 'الوسيط',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'اسم الشريك',
            'applied-on'         => 'تاريخ التقديم',
            'job-position'       => 'المسمى الوظيفي',
            'stage'              => 'المرحلة',
            'candidate-name'     => 'اسم المرشح',
            'evaluation'         => 'التقييم',
            'application-status' => 'حالة الطلب',
            'tags'               => 'الوسوم',
            'refuse-reason'      => 'سبب الرفض',
            'email'              => 'البريد الإلكتروني',
            'recruiter'          => 'المُوظِّف',
            'interviewer'        => 'المُقابِل',
            'candidate-phone'    => 'الهاتف',
            'medium'             => 'الوسيط',
            'source'             => 'المصدر',
            'salary-expected'    => 'الراتب المتوقع',
            'availability-date'  => 'تاريخ التوفر',
        ],

        'filters' => [
            'source'                  => 'المصدر',
            'medium'                  => 'الوسيط',
            'candidate'               => 'المرشح',
            'priority'                => 'الأولوية',
            'salary-proposed-extra'   => 'المزايا الإضافية المقترحة',
            'salary-expected-extra'   => 'المزايا الإضافية المتوقعة',
            'applicant-notes'         => 'ملاحظات المتقدم',
            'create-date'             => 'تاريخ التقديم',
            'date-closed'             => 'تاريخ التوظيف',
            'date-last-stage-updated' => 'آخر تحديث للمرحلة',
            'stage'                   => 'المرحلة',
            'job-position'            => 'المسمى الوظيفي',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المتقدم',
                    'body'  => 'تم حذف المتقدم بنجاح.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'المرحلة',
            'job-position'   => 'المسمى الوظيفي',
            'candidate-name' => 'اسم المرشح',
            'responsible'    => 'المسؤول',
            'creation-date'  => 'تاريخ الإنشاء',
            'hired-date'     => 'تاريخ التوظيف',
            'last-stage'     => 'آخر مرحلة',
            'refuse-reason'  => 'سبب الرفض',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الموظفين',
                    'body'  => 'تم حذف الموظفين بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الموظفين',
                    'body'  => 'تم حذف الموظفين بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الموظفين',
                    'body'  => 'تم استعادة الموظفين بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'evaluation-good'           => 'التقييم: جيد',
                    'evaluation-very-good'      => 'التقييم: جيد جداً',
                    'evaluation-very-excellent' => 'التقييم: ممتاز',
                    'hired'                     => 'تم التوظيف',
                    'candidate-name'            => 'اسم المرشح',
                    'email'                     => 'البريد الإلكتروني',
                    'phone'                     => 'الهاتف',
                    'linkedin-profile'          => 'ملف LinkedIn',
                    'recruiter'                 => 'المُوظِّف',
                    'interviewer'               => 'المُقابِل',
                    'tags'                      => 'الوسوم',
                    'notes'                     => 'ملاحظات',
                    'job-position'              => 'المسمى الوظيفي',
                ],
            ],

            'education-and-availability' => [
                'title' => 'التعليم والتوفر',

                'entries' => [
                    'degree'            => 'الدرجة العلمية',
                    'availability-date' => 'تاريخ التوفر',
                ],
            ],

            'department' => [
                'title' => 'القسم',
            ],

            'salary' => [
                'title' => 'الراتب المتوقع والمقترح',

                'entries' => [
                    'expected-salary'       => 'الراتب المتوقع',
                    'salary-proposed-extra' => 'مزايا أخرى',
                    'proposed-salary'       => 'الراتب المقترح',
                    'salary-expected-extra' => 'مزايا أخرى',
                ],
            ],

            'source-and-medium' => [
                'title' => 'المصدر والوسيط',

                'entries' => [
                    'source' => 'المصدر',
                    'medium' => 'الوسيط',
                ],
            ],
        ],
    ],
];
