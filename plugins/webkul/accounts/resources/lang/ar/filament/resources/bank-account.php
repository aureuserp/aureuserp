<?php

return [
    'navigation' => [
        'title' => 'الحسابات البنكية',
        'group' => 'البنوك',
    ],

    'form' => [
        'account-number'     => 'رقم الحساب',
        'bank'               => [
            'title'    => 'البنك',
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
                        'state'   => 'المنطقة',
                        'zip'     => 'الرمز البريدي',
                        'country' => 'البلد',
                    ],
                ],
            ],
        ],

        'account-holder'     => 'صاحب الحساب',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'رقم الحساب',
            'bank'           => 'البنك',
            'account-holder' => 'صاحب الحساب',
            'send-money'     => 'يمكن إرسال الأموال',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
            'deleted-at'     => 'تاريخ الحذف',
        ],

        'filters' => [
            'bank'           => 'البنك',
            'account-holder' => 'صاحب الحساب',
            'creator'        => 'المنشئ',
            'can-send-money' => 'يمكن إرسال الأموال',
        ],

        'groups' => [
            'bank'               => 'البنك',
            'can-send-money'     => 'يمكن إرسال الأموال',
            'created-at'         => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الحساب البنكي',
                    'body'  => 'تم تحديث الحساب البنكي بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الحساب البنكي',
                    'body'  => 'تم استعادة الحساب البنكي بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحساب البنكي',
                    'body'  => 'تم حذف الحساب البنكي بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الحساب البنكي نهائياً',
                    'body'  => 'تم حذف الحساب البنكي نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الحسابات البنكية',
                    'body'  => 'تم استعادة الحسابات البنكية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحسابات البنكية',
                    'body'  => 'تم حذف الحسابات البنكية بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الحسابات البنكية نهائياً',
                    'body'  => 'تم حذف الحسابات البنكية نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
