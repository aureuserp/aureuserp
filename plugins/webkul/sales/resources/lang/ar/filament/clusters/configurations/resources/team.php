<?php

return [
    'title' => 'فرق المبيعات',

    'navigation' => [
        'title' => 'فرق المبيعات',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'فريق المبيعات',
                'status'   => 'الحالة',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'تفاصيل الفريق',
                        'fields' => [
                            'team-leader'            => 'قائد الفريق',
                            'company'                => 'الشركة',
                            'invoiced-target'        => 'هدف الفواتير',
                            'invoiced-target-suffix' => '/ شهر',
                            'color'                  => 'اللون',
                            'members'                => 'الأعضاء',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'المعرف',
            'company'         => 'الشركة',
            'team-leader'     => 'قائد الفريق',
            'name'            => 'الاسم',
            'status'          => 'الحالة',
            'invoiced-target' => 'هدف الفواتير',
            'color'           => 'اللون',
            'created-by'      => 'أنشئ بواسطة',
            'created-at'      => 'تاريخ الإنشاء',
            'updated-at'      => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'        => 'الاسم',
            'team-leader' => 'قائد الفريق',
            'company'     => 'الشركة',
            'created-by'  => 'أنشئ بواسطة',
            'updated-at'  => 'تاريخ التحديث',
            'created-at'  => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'name'        => 'الاسم',
            'company'     => 'الشركة',
            'team-leader' => 'قائد الفريق',
            'created-at'  => 'تاريخ الإنشاء',
            'updated-at'  => 'تاريخ التحديث',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة فريق المبيعات',
                    'body'  => 'تم استعادة فريق المبيعات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فريق المبيعات',
                    'body'  => 'تم حذف فريق المبيعات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف فريق المبيعات نهائياً',
                    'body'  => 'تم حذف فريق المبيعات نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة فرق المبيعات',
                    'body'  => 'تم استعادة فرق المبيعات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فرق المبيعات',
                    'body'  => 'تم حذف فرق المبيعات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف فرق المبيعات نهائياً',
                    'body'  => 'تم حذف فرق المبيعات نهائياً بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء فرق المبيعات',
                    'body'  => 'تم إنشاء فرق المبيعات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'فريق المبيعات',
                'status'   => 'الحالة',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'تفاصيل الفريق',
                        'entries' => [
                            'team-leader'            => 'قائد الفريق',
                            'company'                => 'الشركة',
                            'invoiced-target'        => 'هدف الفواتير',
                            'invoiced-target-suffix' => '/ شهر',
                            'color'                  => 'اللون',
                            'members'                => 'الأعضاء',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
