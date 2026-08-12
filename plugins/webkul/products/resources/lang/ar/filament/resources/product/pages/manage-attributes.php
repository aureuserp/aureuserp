<?php

return [
    'title' => 'السمات',

    'form' => [
        'attribute' => 'السمة',
        'values'    => 'القيم',
    ],

    'table' => [
        'description' => 'تحذير: إضافة أو حذف السمات سيؤدي إلى حذف وإعادة إنشاء المتغيرات الحالية وفقدان تخصيصاتها المحتملة.',

        'header-actions' => [
            'create' => [
                'label' => 'إضافة سمة',

                'notification' => [
                    'title' => 'تم إنشاء السمة',
                    'body'  => 'تم إنشاء السمة بنجاح.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'السمة',
            'values'    => 'القيم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث السمة',
                    'body'  => 'تم تحديث السمة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السمة',
                    'body'  => 'تم حذف السمة بنجاح.',
                ],
            ],
        ],
    ],
];
