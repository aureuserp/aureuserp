<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'معلومات عامة',

                'sections' => [
                    'branch-information' => [
                        'title' => 'معلومات الفرع',

                        'fields' => [
                            'company-name'                => 'اسم الشركة',
                            'registration-number'         => 'رقم التسجيل',
                            'tax-id'                      => 'معرف الضريبة',
                            'tax-id-tooltip'              => 'معرف الضريبة هو معرف فريد لشركتك.',
                            'color'                       => 'اللون',
                            'company-id'                  => 'معرف الشركة',
                            'company-id-tooltip'          => 'معرف الشركة هو معرف فريد لشركتك.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'العلامة التجارية',
                        'fields' => [
                            'branch-logo' => 'شعار الفرع',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'معلومات العنوان',

                'sections' => [
                    'address-information' => [
                        'title' => 'معلومات العنوان',

                        'fields' => [
                            'street1'                => 'الشارع 1',
                            'street2'                => 'الشارع 2',
                            'city'                   => 'المدينة',
                            'zip'                    => 'الرمز البريدي',
                            'country'                => 'الدولة',
                            'country-currency-name'  => 'اسم العملة',
                            'country-phone-code'     => 'رمز الهاتف',
                            'country-code'           => 'الرمز',
                            'country-name'           => 'اسم الدولة',
                            'country-state-required' => 'الولاية مطلوبة',
                            'country-zip-required'   => 'الرمز البريدي مطلوب',
                            'country-create'         => 'إنشاء دولة',
                            'state'                  => 'الولاية',
                            'state-name'             => 'اسم الولاية',
                            'state-code'             => 'رمز الولاية',
                            'zip-code'               => 'الرمز البريدي',
                            'state-create'           => 'إنشاء ولاية',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'معلومات إضافية',

                        'fields' => [
                            'default-currency'        => 'العملة الافتراضية',
                            'currency-name'           => 'اسم العملة',
                            'currency-full-name'      => 'الاسم الكامل للعملة',
                            'currency-symbol'         => 'رمز العملة',
                            'currency-iso-numeric'    => 'الرمز العددي ISO للعملة',
                            'currency-decimal-places' => 'عدد المنازل العشرية للعملة',
                            'currency-rounding'       => 'تقريب العملة',
                            'currency-status'         => 'حالة العملة',
                            'currency-create'         => 'إنشاء عملة',
                            'company-foundation-date' => 'تاريخ تأسيس الشركة',
                            'status'                  => 'الحالة',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'معلومات الاتصال',

                'sections' => [
                    'contact-information' => [
                        'title' => 'معلومات الاتصال',

                        'fields' => [
                            'email-address' => 'عنوان البريد الإلكتروني',
                            'phone-number'  => 'رقم الهاتف',
                            'mobile-number' => 'رقم الهاتف',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'الشعار',
            'company-name'         => 'اسم الفرع',
            'branches'             => 'الفروع',
            'email'                => 'البريد الإلكتروني',
            'city'                 => 'المدينة',
            'country'              => 'الدولة',
            'currency'             => 'العملة',
            'status'               => 'الحالة',
            'created-at'           => 'تاريخ الإنشاء',
            'updated-at'           => 'تاريخ التحديث',
        ],

        'groups' => [
            'company-name' => 'اسم الفرع',
            'city'         => 'المدينة',
            'country'      => 'الدولة',
            'state'        => 'الولاية',
            'email'        => 'البريد الإلكتروني',
            'phone'        => 'الهاتف',
            'currency'     => 'العملة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'filters' => [
            'trashed' => 'محذوفة',
            'status'  => 'الحالة',
            'country' => 'الدولة',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الفرع',
                    'body'  => 'تم إنشاء الفرع بنجاح.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفرع',
                    'body'  => 'تم تحديث الفرع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفرع',
                    'body'  => 'تم حذف الفرع بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفرع',
                    'body'  => 'تم استعادة الفرع بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفروع',
                    'body'  => 'تم استعادة الفروع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفروع',
                    'body'  => 'تم حذف الفروع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الفروع نهائياً',
                    'body'  => 'تم حذف الفروع نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'معلومات عامة',

                'sections' => [
                    'branch-information' => [
                        'title' => 'معلومات الفرع',

                        'entries' => [
                            'company-name'                => 'اسم الشركة',
                            'registration-number'         => 'رقم التسجيل',
                            'registration-number-tooltip' => 'معرف الضريبة هو معرف فريد لشركتك.',
                            'color'                       => 'اللون',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'العلامة التجارية',
                        'entries' => [
                            'branch-logo' => 'شعار الفرع',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'معلومات العنوان',

                'sections' => [
                    'address-information' => [
                        'title' => 'معلومات العنوان',

                        'entries' => [
                            'street1'                => 'الشارع 1',
                            'street2'                => 'الشارع 2',
                            'city'                   => 'المدينة',
                            'zip'                    => 'الرمز البريدي',
                            'country'                => 'الدولة',
                            'country-currency-name'  => 'اسم العملة',
                            'country-phone-code'     => 'رمز الهاتف',
                            'country-code'           => 'الرمز',
                            'country-name'           => 'اسم الدولة',
                            'country-state-required' => 'الولاية مطلوبة',
                            'country-zip-required'   => 'الرمز البريدي مطلوب',
                            'country-create'         => 'إنشاء دولة',
                            'state'                  => 'الولاية',
                            'state-name'             => 'اسم الولاية',
                            'state-code'             => 'رمز الولاية',
                            'zip-code'               => 'الرمز البريدي',
                            'state-create'           => 'إنشاء ولاية',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'معلومات إضافية',

                        'entries' => [
                            'default-currency'        => 'العملة الافتراضية',
                            'currency-name'           => 'اسم العملة',
                            'currency-full-name'      => 'الاسم الكامل للعملة',
                            'currency-symbol'         => 'رمز العملة',
                            'currency-iso-numeric'    => 'الرمز العددي ISO للعملة',
                            'currency-decimal-places' => 'عدد المنازل العشرية للعملة',
                            'currency-rounding'       => 'تقريب العملة',
                            'currency-status'         => 'حالة العملة',
                            'currency-create'         => 'إنشاء عملة',
                            'company-foundation-date' => 'تاريخ تأسيس الشركة',
                            'status'                  => 'الحالة',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'معلومات الاتصال',

                'sections' => [
                    'contact-information' => [
                        'title' => 'معلومات الاتصال',

                        'entries' => [
                            'email-address' => 'عنوان البريد الإلكتروني',
                            'phone-number'  => 'رقم الهاتف',
                            'mobile-number' => 'رقم الهاتف',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
