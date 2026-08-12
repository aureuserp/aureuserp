<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'شرط الدفع',
                'company'              => 'الشركة',
                'company-placeholder'  => 'كل الشركات',
                'early-discount'       => 'خصم السداد المبكر',
                'discount-days-prefix' => 'إذا تم الدفع خلال',
                'discount-days-suffix' => 'أيام',
                'reduced-tax'          => 'ضريبة مخفضة',
                'note'                 => 'ملاحظة',
                'status'               => 'الحالة',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'شروط الاستحقاق',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'القيمة',
                            'due'                    => 'المستحق',
                            'delay-type'             => 'نوع التأخير',
                            'days-on-the-next-month' => 'أيام في الشهر التالي',
                            'days'                   => 'أيام',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'شرط الدفع',
            'company'      => 'الشركة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'company-name'        => 'اسم الشركة',
            'discount-days'       => 'أيام الخصم',
            'early-pay-discount'  => 'خصم السداد المبكر',
            'payment-term'        => 'شرط الدفع',
            'display-on-invoice'  => 'عرض على الفاتورة',
            'early-discount'      => 'الخصم المبكر',
            'discount-percentage' => 'نسبة الخصم',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة شرط الدفع',
                    'body'  => 'تم استعادة شرط الدفع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط الدفع',
                    'body'  => 'تم حذف شرط الدفع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم الحذف النهائي لشرط الدفع',
                        'body'  => 'تم الحذف النهائي لشرط الدفع بنجاح.',
                    ],

                    'error' => [
                        'title' => 'فشل الحذف النهائي لشرط الدفع',
                        'body'  => 'لا يمكن حذف شرط الدفع نهائياً لأنه مرتبط بقيود يومية.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة شروط الدفع',
                    'body'  => 'تم استعادة شروط الدفع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شروط الدفع',
                    'body'  => 'تم حذف شروط الدفع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم الحذف النهائي لشروط الدفع',
                        'body'  => 'تم الحذف النهائي لشروط الدفع بنجاح.',
                    ],

                    'error' => [
                        'title' => 'فشل الحذف النهائي لشروط الدفع',
                        'body'  => 'لا يمكن حذف شروط الدفع نهائياً لأنها مرتبطة بقيود يومية.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'شرط الدفع',
                'early-discount'       => 'خصم السداد المبكر',
                'discount-percentage'  => 'نسبة الخصم',
                'discount-days-prefix' => 'إذا تم الدفع خلال',
                'discount-days-suffix' => 'أيام',
                'reduced-tax'          => 'ضريبة مخفضة',
                'note'                 => 'ملاحظة',
                'status'               => 'الحالة',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'شروط الاستحقاق',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'القيمة',
                            'due'                    => 'المستحق',
                            'delay-type'             => 'نوع التأخير',
                            'days-on-the-next-month' => 'أيام في الشهر التالي',
                            'days'                   => 'أيام',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
