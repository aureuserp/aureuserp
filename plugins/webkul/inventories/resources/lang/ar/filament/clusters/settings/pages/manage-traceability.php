<?php

return [
    'title' => 'إدارة التتبع',

    'form' => [
        'enable-lots-serial-numbers'                             => 'الدفعات والأرقام التسلسلية',
        'enable-lots-serial-numbers-helper-text'                 => 'الحصول على تتبع كامل من الموردين إلى العملاء',
        'configure-lots'                                         => 'إعداد الدفعات',
        'enable-expiration-dates'                                => 'تواريخ انتهاء الصلاحية',
        'enable-expiration-dates-helper-text'                    => 'تعيين تواريخ انتهاء الصلاحية على الدفعات والأرقام التسلسلية',
        'display-on-delivery-slips'                              => 'العرض على إيصالات التسليم',
        'display-on-delivery-slips-helper-text'                  => 'ستظهر الدفعات والأرقام التسلسلية على إيصالات التسليم',
        'display-expiration-dates-on-delivery-slips'             => 'عرض تواريخ انتهاء الصلاحية على إيصالات التسليم',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'ستظهر تواريخ انتهاء الصلاحية على إيصال التسليم',
        'enable-consignments'                                    => 'الأمانات',
        'enable-consignments-helper-text'                        => 'تعيين المالك على المنتجات المخزنة',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'لديك منتجات في المخزون مع تفعيل تتبع الدفعة/الرقم التسلسلي.',
                'body'  => 'قم أولاً بإيقاف التتبع على جميع المنتجات قبل إيقاف هذا الإعداد.',
            ],
        ],
    ],
];
