<?php

return [
    'navigation' => [
        'title' => 'أوامر التصنيع',
        'group' => 'العمليات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'product'                => 'المنتج',
                    'quantity'               => 'الكمية',
                    'uom'                    => 'وحدة القياس',
                    'bill-of-material'       => 'قائمة المواد',
                    'scheduled-date'         => 'التاريخ المجدول',
                    'scheduled-end'          => 'نهاية الجدول',
                    'responsible'            => 'المسؤول',
                    'to-produce'             => 'للإنتاج',
                    'to-produce-placeholder' => 'معاينة الصورة',
                    'uom-placeholder'        => 'وحدة القياس',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'المكونات',
                'add-action'   => 'إضافة سطر',
                'process-note' => 'سيتم إنشاء المكونات عند بناء عملية التصنيع.',
                'columns'      => [
                    'component'          => 'المنتج',
                    'from'               => 'من',
                    'to-consume'         => 'للاستهلاك',
                    'to-consume-tooltip' => 'الكمية المتاحة غير كافية',
                    'quantity'           => 'الكمية',
                    'uom'                => 'وحدة القياس',
                    'forecast'           => 'التوقع',
                ],
            ],
            'work-orders' => [
                'title'        => 'أوامر العمل',
                'add-action'   => 'إضافة سطر',
                'process-note' => 'سيتم إنشاء أوامر العمل بعد تكوين عملية التصنيع.',
                'columns'      => [
                    'operation'          => 'العملية',
                    'work-center'        => 'مركز العمل',
                    'product'            => 'المنتج',
                    'quantity-remaining' => 'الكمية المتبقية',
                    'quantity-produced'  => 'الكمية المنتجة',
                    'start'              => 'البدء',
                    'end'                => 'الانتهاء',
                    'expected-duration'  => 'المدة المتوقعة',
                    'real-duration'      => 'المدة الفعلية',
                    'status'             => 'الحالة',
                    'lot-serial'         => 'رقم الدفعة/التسلسل',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'فتح أمر العمل',
                    ],

                    'done' => [
                        'label' => 'تم',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'المنتجات الثانوية',
                'process-note' => 'سيتم إنشاء المنتجات الثانوية عند بناء عملية التصنيع.',
                'columns'      => [
                    'product'    => 'المنتج',
                    'to'         => 'إلى',
                    'to-produce' => 'للإنتاج',
                    'uom'        => 'وحدة القياس',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'متفرقات',
                'fields' => [
                    'operation-type'             => 'نوع العملية',
                    'source'                     => 'المصدر',
                    'finished-products-location' => 'موقع المنتجات النهائية',
                    'company'                    => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'المرجع',
            'start'                  => 'البداية',
            'end'                    => 'النهاية',
            'deadline'               => 'الموعد النهائي',
            'product'                => 'المنتج',
            'lot-serial-number'      => 'رقم الدفعة/التسلسل',
            'bill-of-material'       => 'قائمة المواد',
            'source'                 => 'المصدر',
            'responsible'            => 'المسؤول',
            'mo-readiness'           => 'جاهزية أمر التصنيع',
            'component-status'       => 'حالة المكونات',
            'quantity'               => 'الكمية',
            'uom'                    => 'وحدة القياس',
            'consumption-efficiency' => 'كفاءة الاستهلاك',
            'expected-duration'      => 'المدة المتوقعة',
            'real-duration'          => 'المدة الفعلية',
            'company'                => 'الشركة',
            'state'                  => 'الحالة',
        ],
        'groups' => [
            'state'            => 'الحالة',
            'product'          => 'المنتج',
            'bill-of-material' => 'قائمة المواد',
            'responsible'      => 'المسؤول',
            'deadline'         => 'الموعد النهائي',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'عام',
                'entries' => [
                    'product'                => 'المنتج',
                    'scheduled-date'         => 'التاريخ المجدول',
                    'responsible'            => 'المسؤول',
                    'quantity'               => 'الكمية',
                    'uom'                    => 'وحدة القياس',
                    'bill-of-material'       => 'قائمة المواد',
                    'operation-type'         => 'نوع العملية',
                    'consumption-efficiency' => 'كفاءة الاستهلاك',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'المكونات',
                'process-note' => 'ستكون المكونات متاحة بعد تكوين عملية التصنيع.',
                'columns'      => [
                    'component' => 'المكون',
                    'quantity'  => 'الكمية',
                    'uom'       => 'وحدة القياس',
                ],
            ],
            'work-orders' => [
                'title'        => 'أوامر العمل',
                'process-note' => 'ستكون أوامر العمل متاحة بعد تكوين عملية التصنيع.',
                'columns'      => [
                    'operation'          => 'العملية',
                    'work-center'        => 'مركز العمل',
                    'product'            => 'المنتج',
                    'quantity-remaining' => 'الكمية المتبقية',
                    'expected-duration'  => 'المدة المتوقعة',
                    'real-duration'      => 'المدة الفعلية',
                    'lot-serial'         => 'رقم الدفعة/التسلسل',
                    'start'              => 'البدء',
                    'end'                => 'الانتهاء',
                ],
            ],
            'by-products' => [
                'title'        => 'المنتجات الثانوية',
                'process-note' => 'ستكون المنتجات الثانوية متاحة بعد تكوين عملية التصنيع.',
                'columns'      => [
                    'product'    => 'المنتج',
                    'to'         => 'إلى',
                    'to-produce' => 'للإنتاج',
                    'uom'        => 'وحدة القياس',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'متفرقات',
                'entries' => [
                    'operation-type'             => 'نوع العملية',
                    'source'                     => 'المصدر',
                    'finished-products-location' => 'موقع المنتجات النهائية',
                    'company'                    => 'الشركة',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'تأكيد',
                    'notification' => [
                        'title' => 'تم تأكيد أمر التصنيع',
                    ],
                ],

                'cancel' => [
                    'label'        => 'إلغاء',
                    'notification' => [
                        'title' => 'تم إلغاء أمر التصنيع',
                    ],
                ],
            ],
        ],
    ],
];
