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

            'delete' => [
                'label' => 'أرشفة',

                'modal' => [
                    'heading'     => 'أرشفة سبب الخسارة',
                    'description' => 'سينتقل سبب الخسارة إلى علامة التبويب المؤرشفة. يمكنك استعادته في أي وقت.',
                ],

                'notification' => [
                    'title' => 'تمت أرشفة سبب الخسارة',
                    'body'  => 'تمت أرشفة سبب الخسارة بنجاح.',
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
