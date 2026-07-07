<?php

return [
    'title' => 'الشركات',

    'navigation' => [
        'title' => 'الشركات',
    ],

    'global-search' => [
        'email' => 'البريد الإلكتروني',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'معلومات الشركة',
                'fields' => [
                    'name'                  => 'اسم الشركة',
                    'registration-number'   => 'رقم التسجيل',
                    'company-id'            => 'معرف الشركة',
                    'tax-id'                => 'الرقم الضريبي',
                    'tax-id-tooltip'        => 'الرقم الضريبي هو معرف فريد لشركتك.',
                    'website'               => 'الموقع الإلكتروني',
                ],
            ],

            'address-information' => [
                'title'  => 'معلومات العنوان',

                'fields' => [
                    'street1'        => 'الشارع 1',
                    'street2'        => 'الشارع 2',
                    'city'           => 'المدينة',
                    'zipcode'        => 'الرمز البريدي',
                    'country'        => 'الدولة',
                    'currency-name'  => 'اسم العملة',
                    'phone-code'     => 'رمز الهاتف',
                    'code'           => 'الرمز',
                    'country-name'   => 'اسم الدولة',
                    'state-required' => 'المنطقة مطلوبة',
                    'zip-required'   => 'الرمز البريدي مطلوب',
                    'create-country' => 'إنشاء دولة',
                    'state'          => 'المنطقة',
                    'state-name'     => 'اسم المنطقة',
                    'state-code'     => 'رمز المنطقة',
                    'create-state'   => 'إنشاء منطقة',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'fields' => [
                    'default-currency'        => 'العملة الافتراضية',
                    'currency-name'           => 'اسم العملة',
                    'currency-full-name'      => 'الاسم الكامل للعملة',
                    'currency-symbol'         => 'رمز العملة',
                    'currency-iso-numeric'    => 'رقم ISO للعملة',
                    'currency-decimal-places' => 'المنازل العشرية للعملة',
                    'currency-rounding'       => 'تقريب العملة',
                    'currency-status'         => 'حالة العملة',
                    'company-foundation-date' => 'تاريخ تأسيس الشركة',
                    'currency-create'         => 'إنشاء عملة',
                    'status'                  => 'الحالة',
                ],
            ],

            'branding' => [
                'title'  => 'الهوية البصرية',
                'fields' => [
                    'company-logo' => 'شعار الشركة',
                    'color'        => 'اللون',
                ],
            ],

            'contact-information' => [
                'title'  => 'معلومات الاتصال',
                'fields' => [
                    'email'  => 'البريد الإلكتروني',
                    'phone'  => 'رقم الهاتف',
                    'mobile' => 'رقم الجوال',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'         => 'الشعار',
            'company-name' => 'اسم الشركة',
            'branches'     => 'الفروع',
            'email'        => 'البريد الإلكتروني',
            'city'         => 'المدينة',
            'country'      => 'الدولة',
            'currency'     => 'العملة',
            'created-by'   => 'أُنشئ بواسطة',
            'status'       => 'الحالة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'company-name' => 'اسم الشركة',
            'city'         => 'المدينة',
            'country'      => 'الدولة',
            'state'        => 'المنطقة',
            'email'        => 'البريد الإلكتروني',
            'phone'        => 'الهاتف',
            'currency'     => 'العملة',
            'created-by'   => 'أُنشئ بواسطة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'filters' => [
            'status'  => 'الحالة',
            'country' => 'الدولة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تعديل الشركة',
                    'body'  => 'تم تعديل الشركة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الشركة',
                    'body'  => 'تم حذف الشركة بنجاح.',

                    'default-company' => [
                        'title' => 'لا يمكن حذف الشركة',
                        'body'  => 'تم تعيين هذه الشركة كشركة افتراضية في إعدادات إدارة المستخدمين. يرجى تغيير الشركة الافتراضية قبل الحذف.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الشركة',
                    'body'  => 'تم استعادة الشركة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الشركة نهائياً',
                        'body'  => 'تم حذف الشركة نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف الشركة نهائياً',
                        'body'  => 'هذه الشركة مرتبطة بسجلات موجودة ولا يمكن حذفها.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الشركات',
                    'body'  => 'تم استعادة الشركات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الشركات',
                    'body'  => 'تم حذف الشركات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الشركات نهائياً',
                    'body'  => 'تم حذف الشركات نهائياً بنجاح.',
                    'error' => [
                        'title' => 'تعذر حذف الشركات نهائياً',
                        'body'  => 'شركة واحدة أو أكثر مرتبطة بسجلات موجودة ولا يمكن حذفها.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الشركات',
                    'body'  => 'تم إنشاء الشركات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'معلومات الشركة',
                'entries' => [
                    'name'                  => 'اسم الشركة',
                    'registration-number'   => 'رقم التسجيل',
                    'company-id'            => 'معرف الشركة',
                    'tax-id'                => 'الرقم الضريبي',
                    'tax-id-tooltip'        => 'الرقم الضريبي هو معرف فريد لشركتك.',
                    'website'               => 'الموقع الإلكتروني',
                ],
            ],

            'address-information' => [
                'title'  => 'معلومات العنوان',

                'entries' => [
                    'street1'        => 'الشارع 1',
                    'street2'        => 'الشارع 2',
                    'city'           => 'المدينة',
                    'zipcode'        => 'الرمز البريدي',
                    'country'        => 'الدولة',
                    'currency-name'  => 'اسم العملة',
                    'phone-code'     => 'رمز الهاتف',
                    'code'           => 'الرمز',
                    'country-name'   => 'اسم الدولة',
                    'state-required' => 'المنطقة مطلوبة',
                    'zip-required'   => 'الرمز البريدي مطلوب',
                    'create-country' => 'إنشاء دولة',
                    'state'          => 'المنطقة',
                    'state-name'     => 'اسم المنطقة',
                    'state-code'     => 'رمز المنطقة',
                    'create-state'   => 'إنشاء منطقة',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'entries' => [
                    'default-currency'        => 'العملة الافتراضية',
                    'currency-name'           => 'اسم العملة',
                    'currency-full-name'      => 'الاسم الكامل للعملة',
                    'currency-symbol'         => 'رمز العملة',
                    'currency-iso-numeric'    => 'رقم ISO للعملة',
                    'currency-decimal-places' => 'المنازل العشرية للعملة',
                    'currency-rounding'       => 'تقريب العملة',
                    'currency-status'         => 'حالة العملة',
                    'company-foundation-date' => 'تاريخ تأسيس الشركة',
                    'currency-create'         => 'إنشاء عملة',
                    'status'                  => 'الحالة',
                ],
            ],

            'branding' => [
                'title'   => 'الهوية البصرية',
                'entries' => [
                    'company-logo' => 'شعار الشركة',
                    'color'        => 'اللون',
                ],
            ],

            'contact-information' => [
                'title'   => 'معلومات الاتصال',
                'entries' => [
                    'email'  => 'البريد الإلكتروني',
                    'phone'  => 'رقم الهاتف',
                    'mobile' => 'رقم الجوال',
                ],
            ],
        ],
    ],
];
