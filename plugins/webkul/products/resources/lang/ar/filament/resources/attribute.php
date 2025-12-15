<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name' => 'الاسم',
                    'type' => 'النوع',
                ],
            ],

            'options' => [
                'title'  => 'الخيارات',

                'fields' => [
                    'name'        => 'الاسم',
                    'color'       => 'اللون',
                    'extra-price' => 'السعر الإضافي',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'الاسم',
            'type'        => 'النوع',
            'deleted-at'  => 'تاريخ الحذف',
            'created-at'  => 'تاريخ الإنشاء',
            'updated-at'  => 'تاريخ التحديث',
        ],

        'groups' => [
            'type'       => 'النوع',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'type' => 'النوع',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة السمة',
                    'body'  => 'تم استعادة السمة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السمة',
                    'body'  => 'تم حذف السمة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف السمة نهائياً',
                        'body'  => 'تم حذف السمة نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف السمة',
                        'body'  => 'لا يمكن حذف السمة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة السمات',
                    'body'  => 'تم استعادة السمات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السمات',
                    'body'  => 'تم حذف السمات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف السمات نهائياً',
                        'body'  => 'تم حذف السمات نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف السمات',
                        'body'  => 'لا يمكن حذف السمات لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name' => 'الاسم',
                    'type' => 'النوع',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'creator'    => 'أنشئ بواسطة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
