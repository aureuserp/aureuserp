<?php

return [
    'model-label' => 'تسلسل',

    'plural-model-label' => 'التسلسلات',

    'navigation' => [
        'title' => 'التسلسلات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',
                'description' => 'تُنشأ تسلسلات دفاتر اليومية والمستودعات وأنواع العمليات تلقائيًا عند إنشاء تلك السجلات — يمكنك تعديلها هنا. الإنشاء اليدوي مطلوب فقط للتسلسلات المخصصة القائمة على الرمز.',

                'fields' => [
                    'name'      => 'الاسم',
                    'code'      => 'الرمز',
                    'code-help' => 'معرّف تقني تستخدمه المستندات، مثل sales.order. التسلسلات المنشأة تلقائيًا تحمل الرمز الصحيح بالفعل.',
                    'company'   => 'الشركة',
                ],
            ],

            'format' => [
                'title' => 'الترقيم',

                'fields' => [
                    'prefix'          => 'البادئة',
                    'prefix-help'     => 'العناصر النائبة: %(year)، %(y)، %(month)، %(day). مثال: INV/%(year)/',
                    'suffix'          => 'اللاحقة',
                    'padding'         => 'عدد خانات الرقم',
                    'next-number'     => 'الرقم التالي',
                    'next-number-help' => 'لا يمكن إلا زيادته. لإعادة بدء الترقيم بأمان، احذف التسلسل وسيُعاد إنشاؤه من أعلى رقم مستند موجود.',
                    'step'            => 'الخطوة',
                    'reset-frequency' => 'إعادة تعيين العداد',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'الاسم',
            'code'            => 'الرمز',
            'applies-to'      => 'ينطبق على',
            'company'         => 'الشركة',
            'next-preview'    => 'رقم المستند التالي',
            'next-number'     => 'الرقم التالي',
            'reset-frequency' => 'إعادة تعيين العداد',
        ],

        'variants' => [
            'refund'         => 'استرداد',
            'payment'        => 'دفع',
            'refund-payment' => 'استرداد + دفع',
        ],

        'filters' => [
            'company' => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث التسلسل',
                    'body'  => 'تم تحديث التسلسل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف التسلسل',
                    'body'  => 'تم حذف التسلسل بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف التسلسلات',
                    'body'  => 'تم حذف التسلسلات بنجاح.',
                ],
            ],
        ],
    ],
];
