<?php

return [
    'navigation' => [
        'title' => 'التجديد',
        'group' => 'المشتريات',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'المنتج',
            'location'          => 'الموقع',
            'route'             => 'المسار',
            'vendor'            => 'المورد',
            'trigger'           => 'المُفعِّل',
            'on-hand'           => 'المتاح',
            'min'               => 'الحد الأدنى',
            'max'               => 'الحد الأقصى',
            'multiple-quantity' => 'الكمية المتعددة',
            'to-order'          => 'للطلب',
            'uom'               => 'وحدة القياس',
            'company'           => 'الشركة',
        ],

        'groups' => [
            'location' => 'الموقع',
            'product'  => 'المنتج',
            'category' => 'الفئة',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'إضافة تجديد',

                'notification' => [
                    'title' => 'تمت إضافة التجديد',
                    'body'  => 'تمت إضافة التجديد بنجاح.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'التجديد موجود بالفعل',
                        'body'  => 'يوجد تجديد بالفعل لهذه الإعدادات. يرجى تحديث التجديد الموجود بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
