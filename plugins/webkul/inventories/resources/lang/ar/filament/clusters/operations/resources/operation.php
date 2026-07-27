<?php

return [
    'navigation' => [
        'title' => 'المنتجات',
        'group' => 'المخزون',
    ],

    'global-search' => [
        'partner' => 'الشريك',
        'origin'  => 'المصدر',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'receive-from'         => 'الاستلام من',
                    'contact'              => 'جهة الاتصال',
                    'delivery-address'     => 'عنوان التسليم',
                    'operation-type'       => 'نوع العملية',
                    'source-location'      => 'موقع المصدر',
                    'destination-location' => 'موقع الوجهة',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'العمليات',

                'columns' => [
                    'product'                    => 'المنتج',
                    'final-location'             => 'الموقع النهائي',
                    'description'                => 'الوصف',
                    'scheduled-at'               => 'موعد الجدولة',
                    'deadline'                   => 'الموعد النهائي',
                    'packaging'                  => 'التعبئة',
                    'demand'                     => 'الطلب',
                    'quantity'                   => 'الكمية',
                    'insufficient-stock-tooltip' => 'الكمية المتاحة غير كافية',
                    'unit'                       => 'الوحدة',
                    'picked'                     => 'تم الانتقاء',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'فتح المنتج',
                    ],
                ],

                'fields' => [
                    'product'        => 'المنتج',
                    'final-location' => 'الموقع النهائي',
                    'description'    => 'الوصف',
                    'scheduled-at'   => 'موعد الجدولة',
                    'deadline'       => 'الموعد النهائي',
                    'packaging'      => 'التعبئة',
                    'demand'         => 'الطلب',
                    'quantity'       => 'الكمية',
                    'unit'           => 'الوحدة',
                    'picked'         => 'تم الانتقاء',

                    'lines' => [
                        'modal-heading' => 'إدارة حركات المخزون',
                        'modal-submit-action-label' => 'حفظ',
                        'add-line'      => 'إضافة سطر',

                        'actions' => [
                            'generate' => 'توليد الأرقام التسلسلية/الدفعات',
                            'import'   => 'استيراد الأرقام التسلسلية/الدفعات',
                        ],

                        'fields' => [
                            'lot'                => 'الدفعة/الرقم التسلسلي',
                            'pick-from'          => 'الانتقاء من',
                            'location'           => 'التخزين في',
                            'package'            => 'طرد الوجهة',
                            'quantity'           => 'الكمية',
                            'uom'                => 'وحدة القياس',
                            'first-lot'          => 'رقم الدفعة الأول',
                            'quantity-per-lot'   => 'الكمية لكل دفعة',
                            'quantity-received'  => 'الكمية المستلمة',
                            'keep-current-lines' => 'الإبقاء على السطور الحالية',
                            'serials'            => 'الأرقام التسلسلية/الدفعات',
                            'serials-helper'     => 'رقم تسلسلي/دفعة واحد لكل سطر.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'إضافي',

                'fields' => [
                    'responsible'                  => 'المسؤول',
                    'shipping-policy'              => 'سياسة الشحن',
                    'shipping-policy-hint-tooltip' => 'تحدد ما إذا كان يجب تسليم البضائع جزئياً أو دفعة واحدة.',
                    'scheduled-at'                 => 'موعد الجدولة',
                    'scheduled-at-hint-tooltip'    => 'الوقت المجدول لمعالجة الجزء الأول من الشحنة. تعيين قيمة يدوياً هنا سيطبقها كتاريخ متوقع لجميع حركات المخزون.',
                    'source-document'              => 'المستند المصدر',
                    'source-document-hint-tooltip' => 'مرجع المستند',
                ],
            ],

            'note' => [
                'title' => 'ملاحظة',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'مفضل',
            'reference'       => 'المرجع',
            'from'            => 'من',
            'to'              => 'إلى',
            'contact'         => 'جهة الاتصال',
            'responsible'     => 'المسؤول',
            'scheduled-at'    => 'موعد الجدولة',
            'deadline'        => 'الموعد النهائي',
            'closed-at'       => 'تاريخ الإغلاق',
            'source-document' => 'المستند المصدر',
            'operation-type'  => 'نوع العملية',
            'company'         => 'الشركة',
            'state'           => 'الحالة',
            'deleted-at'      => 'تاريخ الحذف',
            'created-at'      => 'تاريخ الإنشاء',
            'updated-at'      => 'تاريخ التحديث',
        ],

        'groups' => [
            'state'           => 'الحالة',
            'source-document' => 'المستند المصدر',
            'operation-type'  => 'نوع العملية',
            'scheduled-at'    => 'موعد الجدولة',
            'created-at'      => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'operation-type'       => 'نوع العملية',
            'name'                 => 'الاسم',
            'state'                => 'الحالة',
            'partner'              => 'الشريك',
            'responsible'          => 'المسؤول',
            'owner'                => 'المالك',
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الوجهة',
            'deadline'             => 'الموعد النهائي',
            'scheduled-at'         => 'موعد الجدولة',
            'closed-at'            => 'تاريخ الإغلاق',
            'created-at'           => 'تاريخ الإنشاء',
            'updated-at'           => 'تاريخ التحديث',
            'company'              => 'الشركة',
            'creator'              => 'المُنشئ',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'معلومات عامة',
                'entries' => [
                    'contact'              => 'جهة الاتصال',
                    'operation-type'       => 'نوع العملية',
                    'source-location'      => 'موقع المصدر',
                    'destination-location' => 'موقع الوجهة',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'العمليات',
                'entries' => [
                    'product'        => 'المنتج',
                    'final-location' => 'الموقع النهائي',
                    'description'    => 'الوصف',
                    'scheduled-at'   => 'موعد الجدولة',
                    'deadline'       => 'الموعد النهائي',
                    'packaging'      => 'التعبئة',
                    'demand'         => 'الطلب',
                    'quantity'       => 'الكمية',
                    'unit'           => 'الوحدة',
                    'picked'         => 'تم الانتقاء',
                ],
            ],
            'additional' => [
                'title'   => 'معلومات إضافية',
                'entries' => [
                    'responsible'     => 'المسؤول',
                    'shipping-policy' => 'سياسة الشحن',
                    'scheduled-at'    => 'موعد الجدولة',
                    'source-document' => 'المستند المصدر',
                ],
            ],
            'note' => [
                'title' => 'ملاحظة',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'للتنفيذ',
        'my'          => 'تحويلاتي',
        'starred'     => 'المميزة',
        'draft'       => 'مسودة',
        'waiting'     => 'في الانتظار',
        'ready'       => 'جاهز',
        'done'        => 'منجز',
        'canceled'    => 'ملغي',
        'back-orders' => 'الطلبات المتأخرة',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'تحذير بشأن دقة وحدة القياس',
            'body'  => 'أنت تستخدم وحدة قياس أصغر من تلك المستخدمة لتخزين هذا المنتج. قد يؤدي ذلك إلى مشاكل في التقريب على الكميات المحجوزة. يُنصح باستخدام أصغر وحدة قياس لتقييم المخزون، أو تقليل دقة التقريب لوحدتك الأساسية.',
        ],
    ],
];
