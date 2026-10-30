<?php

return [
    'model-label'        => 'سبب الخسارة',
    'plural-model-label' => 'أسباب الخسارة',

    'navigation' => [
        'title' => 'أسباب الخسارة',
        'group' => 'مسار المبيعات',
    ],

    'form' => [
        'fields' => [
            'name' => 'الوصف',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الوصف',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث سبب الخسارة',
                    'body'  => 'تم تحديث سبب الخسارة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة سبب الخسارة',
                    'body'  => 'تم استعادة سبب الخسارة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف سبب الخسارة',
                    'body'  => 'تم حذف سبب الخسارة نهائياً.',
                ],
            ],
        ],
    ],
];
