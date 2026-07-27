<?php

return [
    'navigation' => [
        'title' => 'المعدات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'المعلومات العامة',
                'fields' => [
                    'name' => 'الاسم',
                    'note' => 'الوصف',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'category'   => 'فئة المعدة',
                    'team'       => 'فريق الصيانة',
                    'company'    => 'الشركة',
                    'technician' => 'الفني',
                    'owner'      => 'المالك',
                    'location'   => 'الموقع المستخدم فيه',
                ],
            ],

            'product-information' => [
                'title'  => 'معلومات المنتج',
                'fields' => [
                    'partner'                     => 'المورد',
                    'partner-ref'                 => 'مرجع المورد',
                    'model'                       => 'الطراز',
                    'serial-no'                   => 'الرقم التسلسلي',
                    'effective-date'              => 'تاريخ السريان',
                    'effective-date-hint-tooltip' => 'يُستخدم كنقطة بداية لحساب متوسط الوقت بين الأعطال.',
                    'cost'                        => 'التكلفة',
                    'warranty-date'               => 'تاريخ انتهاء الضمان',
                ],
            ],

            'maintenance' => [
                'title'  => 'الصيانة',
                'fields' => [
                    'expected-mtbf' => 'متوسط الوقت المتوقع بين الأعطال',
                ],
                'suffixes' => [
                    'days' => 'أيام',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'اسم المعدة',
            'owner'      => 'المالك',
            'serial-no'  => 'الرقم التسلسلي',
            'category'   => 'فئة المعدة',
            'technician' => 'الفني',
            'company'    => 'الشركة',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'category'   => 'فئة المعدة',
            'team'       => 'فريق الصيانة',
            'technician' => 'الفني',
        ],

        'groups' => [
            'category'   => 'فئة المعدة',
            'owner'      => 'المالك',
            'technician' => 'الفني',
            'vendor'     => 'المورد',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المعدة',
                    'body'  => 'تم تحديث المعدة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة المعدة',
                    'body'  => 'تمت استعادة المعدة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة المعدة',
                    'body'  => 'تمت أرشفة المعدة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المعدة',
                        'body'  => 'تم حذف المعدة نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المعدة',
                        'body'  => 'هذه المعدة مرتبطة بسجل آخر.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة المعدات',
                    'body'  => 'تمت استعادة المعدات المحددة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة المعدات',
                    'body'  => 'تمت أرشفة المعدات المحددة بنجاح.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء المعدة',
                    'body'  => 'تم إنشاء المعدة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'المعلومات العامة',
                'entries' => [
                    'name' => 'الاسم',
                    'note' => 'الوصف',
                ],
            ],

            'settings' => [
                'title'   => 'الإعدادات',
                'entries' => [
                    'category'   => 'فئة المعدة',
                    'team'       => 'فريق الصيانة',
                    'company'    => 'الشركة',
                    'technician' => 'الفني',
                    'owner'      => 'المالك',
                    'location'   => 'الموقع المستخدم فيه',
                ],
            ],

            'product-information' => [
                'title'   => 'معلومات المنتج',
                'entries' => [
                    'partner'        => 'المورد',
                    'partner-ref'    => 'مرجع المورد',
                    'model'          => 'الطراز',
                    'serial-no'      => 'الرقم التسلسلي',
                    'effective-date' => 'تاريخ السريان',
                    'cost'           => 'التكلفة',
                    'warranty-date'  => 'تاريخ انتهاء الضمان',
                ],
            ],

            'maintenance' => [
                'title'   => 'الصيانة',
                'entries' => [
                    'expected-mtbf'          => 'متوسط الوقت المتوقع بين الأعطال',
                    'maintenance-count'      => 'عدد عمليات الصيانة',
                    'maintenance-open-count' => 'عدد عمليات الصيانة المفتوحة',
                    'assigned-at'            => 'تاريخ التعيين',
                    'scraped-at'             => 'تاريخ التخريد',
                ],
                'suffixes' => [
                    'days' => 'أيام',
                ],
            ],
        ],
    ],
];
