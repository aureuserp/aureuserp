<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'مصدر الضريبة',
            'tax-destination' => 'وجهة الضريبة',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'مصدر الضريبة',
            'tax-destination' => 'وجهة الضريبة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث شرط استحقاق الدفع',
                    'body'  => 'تم تحديث شرط استحقاق الدفع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط استحقاق الدفع',
                    'body'  => 'تم حذف شرط استحقاق الدفع بنجاح.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء شرط استحقاق الدفع',
                    'body'  => 'تم إنشاء شرط استحقاق الدفع بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'مصدر الضريبة',
            'tax-destination' => 'وجهة الضريبة',
        ],
    ],
];
