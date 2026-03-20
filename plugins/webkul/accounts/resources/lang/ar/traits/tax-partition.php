<?php

return [
    'form' => [
        'factor-percent'    => 'نسبة العامل',
        'factor-ratio'      => 'معدل العامل',
        'repartition-type'  => 'نوع التوزيع',
        'document-type'     => 'نوع المستند',
        'account'           => 'الحساب',
        'tax'               => 'الضريبة',
        'tax-closing-entry' => 'قيد إغلاق الضريبة',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'نسبة العامل(%)',
            'account'           => 'الحساب',
            'tax'               => 'الضريبة',
            'company'           => 'الشركة',
            'repartition-type'  => 'نوع التوزيع',
            'document-type'     => 'نوع المستند',
            'tax-closing-entry' => 'قيد إغلاق الضريبة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث توزيع الضريبة',
                    'body'  => 'تم تحديث توزيع الضريبة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط توزيع الضريبة',
                    'body'  => 'تم حذف شرط توزيع الضريبة بنجاح.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء شرط توزيع الضريبة',
                    'body'  => 'تم إنشاء شرط توزيع الضريبة بنجاح.',
                ],
            ],
        ],
    ],
];
