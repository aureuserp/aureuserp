<?php

return [
    'title' => 'المرشح',

    'navigation' => [
        'title' => 'المرشحون',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'المعلومات الأساسية',

                'fields' => [
                    'full-name' => 'الاسم الكامل',
                    'email'     => 'البريد الإلكتروني',
                    'phone'     => 'رقم الهاتف',
                    'linkedin'  => 'ملف LinkedIn',
                    'contact'   => 'جهة الاتصال',
                ],
            ],

            'additional-details' => [
                'title' => 'تفاصيل إضافية',

                'fields' => [
                    'company'           => 'الشركة',
                    'degree'            => 'الدرجة العلمية',
                    'tags'              => 'الوسوم',
                    'manager'           => 'المدير',
                    'availability-date' => 'تاريخ التوفر',

                    'priority-options' => [
                        'low'    => 'منخفضة',
                        'medium' => 'متوسطة',
                        'high'   => 'عالية',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'الحالة',

                'fields' => [
                    'active'     => 'نشط',
                    'evaluation' => 'التقييم',
                ],
            ],

            'communication' => [
                'title' => 'التواصل',

                'fields' => [
                    'cc-email'      => 'نسخة البريد الإلكتروني',
                    'email-bounced' => 'بريد مرتجع',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم الكامل',
            'tags'       => 'الوسوم',
            'evaluation' => 'التقييم',
        ],

        'filters' => [
            'company'      => 'الشركة',
            'partner-name' => 'جهة الاتصال',
            'degree'       => 'الدرجة العلمية',
            'manager-name' => 'المدير',
        ],

        'groups' => [
            'manager-name' => 'المدير',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المرشح',
                    'body'  => 'تم حذف المرشح بنجاح.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'تم إنشاء المرشح',
                        'body'  => 'تم إنشاء المرشح بنجاح.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المرشحين',
                    'body'  => 'تم حذف المرشحين بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'المعلومات الأساسية',

                'entries' => [
                    'full-name' => 'الاسم الكامل',
                    'email'     => 'البريد الإلكتروني',
                    'phone'     => 'رقم الهاتف',
                    'linkedin'  => 'ملف LinkedIn',
                    'contact'   => 'جهة الاتصال',
                ],
            ],

            'additional-details' => [
                'title' => 'تفاصيل إضافية',

                'entries' => [
                    'company'           => 'الشركة',
                    'degree'            => 'الدرجة العلمية',
                    'tags'              => 'الوسوم',
                    'manager'           => 'المدير',
                    'availability-date' => 'تاريخ التوفر',

                    'priority-options' => [
                        'low'    => 'منخفضة',
                        'medium' => 'متوسطة',
                        'high'   => 'عالية',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'الحالة',

                'entries' => [
                    'active'     => 'نشط',
                    'evaluation' => 'التقييم',
                ],
            ],

            'communication' => [
                'title' => 'التواصل',

                'entries' => [
                    'cc-email'      => 'نسخة البريد الإلكتروني',
                    'email-bounced' => 'بريد مرتجع',
                ],
            ],
        ],
    ],
];
