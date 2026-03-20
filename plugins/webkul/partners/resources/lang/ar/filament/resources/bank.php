<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'  => 'الاسم',
                    'code'  => 'رمز تعريف البنك',
                    'email' => 'البريد الإلكتروني',
                    'phone' => 'الهاتف',
                ],
            ],

            'address' => [
                'title' => 'العنوان',

                'fields' => [
                    'address' => 'العنوان',
                    'city'    => 'المدينة',
                    'street1' => 'الشارع 1',
                    'street2' => 'الشارع 2',
                    'state'   => 'الولاية',
                    'zip'     => 'الرمز البريدي',
                    'country' => 'الدولة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'الاسم',
            'code'           => 'رمز تعريف البنك',
            'country'        => 'الدولة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
            'deleted-at'     => 'تاريخ الحذف',
        ],

        'groups' => [
            'country'               => 'الدولة',
            'created-at'            => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث البنك',
                    'body'  => 'تم تحديث البنك بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة البنك',
                    'body'  => 'تم استعادة البنك بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف البنك',
                    'body'  => 'تم حذف البنك بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف البنك نهائياً',
                    'body'  => 'تم حذف البنك نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة البنوك',
                    'body'  => 'تم استعادة البنوك بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف البنوك',
                    'body'  => 'تم حذف البنوك بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف البنوك نهائياً',
                    'body'  => 'تم حذف البنوك نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
