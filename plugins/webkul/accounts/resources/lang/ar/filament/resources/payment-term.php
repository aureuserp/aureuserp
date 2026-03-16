<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'شرط الدفع',
                'early-discount'       => 'خصم السداد المبكر',
                'discount-days-prefix' => 'إذا تم الدفع خلال',
                'discount-days-suffix' => 'أيام',
                'reduced-tax'          => 'ضريبة مخفضة',
                'note'                 => 'ملاحظة',
                'status'               => 'الحالة',
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
                    'title' => 'تم حذف شرط الدفع نهائياً',
                    'body'  => 'تم حذف شرط الدفع نهائياً بنجاح.',
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
                    'title' => 'تم حذف شروط الدفع نهائياً',
                    'body'  => 'تم حذف شروط الدفع نهائياً بنجاح.',
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
    ],
];
