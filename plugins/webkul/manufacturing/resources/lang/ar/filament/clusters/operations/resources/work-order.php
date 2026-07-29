<?php

return [
    'navigation' => [
        'title' => 'أوامر العمل',
        'group' => 'العمليات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'work-order'           => 'أمر العمل',
                    'work-center'          => 'مركز العمل',
                    'product'              => 'المنتج',
                    'quantity'             => 'الكمية',
                    'manufacturing-order'  => 'أمر التصنيع',
                    'lot-serial'           => 'رقم الدفعة/التسلسل',
                    'start-date'           => 'تاريخ البدء',
                    'end-date'             => 'تاريخ الانتهاء',
                    'date-range-separator' => 'إلى',
                    'expected-duration'    => 'المدة المتوقعة',
                    'duration-suffix'      => 'دقائق',
                    'real-duration'        => 'المدة الفعلية',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'تتبع الوقت',
                'add-action' => 'إضافة سطر',
                'columns'    => [
                    'user'         => 'المستخدم',
                    'duration'     => 'المدة',
                    'start-date'   => 'تاريخ البدء',
                    'end-date'     => 'تاريخ الانتهاء',
                    'productivity' => 'الإنتاجية',
                ],
                'footer' => [
                    'real-duration' => 'المدة الفعلية',
                ],
            ],
            'components' => [
                'title'      => 'المكونات',
                'add-action' => 'إضافة سطر',
                'columns'    => [
                    'product'    => 'المنتج',
                    'from'       => 'من',
                    'to-consume' => 'للاستهلاك',
                    'quantity'   => 'الكمية',
                    'uom'        => 'وحدة القياس',
                ],
            ],
            'work-instruction' => [
                'title'   => 'تعليمات العمل',
                'entries' => [
                    'operation' => 'العملية',
                    'worksheet' => 'ورقة العمل',
                ],
            ],
            'blocked-by' => [
                'title'  => 'محظور بواسطة',
                'fields' => [
                    'work-orders' => 'أوامر العمل',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'العملية',
            'work-center'         => 'مركز العمل',
            'manufacturing-order' => 'أمر التصنيع',
            'product'             => 'المنتج',
            'quantity-remaining'  => 'الكمية المتبقية',
            'lot-serial'          => 'رقم الدفعة/التسلسل',
            'start'               => 'البدء',
            'end'                 => 'الانتهاء',
            'expected-duration'   => 'المدة المتوقعة',
            'real-duration'       => 'المدة الفعلية',
            'status'              => 'الحالة',
        ],
        'groups' => [
            'status'              => 'الحالة',
            'work-center'         => 'مركز العمل',
            'manufacturing-order' => 'أمر التصنيع',
            'product'             => 'المنتج',
            'start'               => 'البدء',
            'end'                 => 'الانتهاء',
        ],
        'filters' => [
            'work-order'          => 'أمر العمل',
            'status'              => 'الحالة',
            'operation'           => 'العملية',
            'work-center'         => 'مركز العمل',
            'manufacturing-order' => 'أمر التصنيع',
            'product'             => 'المنتج',
            'start'               => 'البدء',
            'end'                 => 'الانتهاء',
            'created-at'          => 'تاريخ الإنشاء',
            'updated-at'          => 'تاريخ التحديث',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'عام',
                'entries' => [
                    'work-order'          => 'أمر العمل',
                    'work-center'         => 'مركز العمل',
                    'product'             => 'المنتج',
                    'quantity'            => 'الكمية',
                    'manufacturing-order' => 'أمر التصنيع',
                    'lot-serial'          => 'رقم الدفعة/التسلسل',
                    'start-date'          => 'تاريخ البدء',
                    'end-date'            => 'تاريخ الانتهاء',
                    'expected-duration'   => 'المدة المتوقعة',
                    'real-duration'       => 'المدة الفعلية',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'تتبع الوقت',
                'footer' => [
                    'real-duration' => 'المدة الفعلية',
                ],
            ],
            'components' => [
                'title' => 'المكونات',
            ],
            'work-instruction' => [
                'title'   => 'تعليمات العمل',
                'entries' => [
                    'operation' => 'العملية',
                    'worksheet' => 'ورقة العمل',
                ],
            ],
            'blocked-by' => [
                'title'   => 'محظور بواسطة',
                'columns' => [
                    'work-order'  => 'أمر العمل',
                    'work-center' => 'مركز العمل',
                    'status'      => 'الحالة',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'أمر عمل جديد',
                ],
            ],
        ],
    ],
];
