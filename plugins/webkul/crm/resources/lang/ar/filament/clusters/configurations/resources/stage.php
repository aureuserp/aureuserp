<?php

return [
    'model-label'        => 'المرحلة',
    'plural-model-label' => 'المراحل',

    'navigation' => [
        'title' => 'المراحل',
        'group' => 'مسار المبيعات',
    ],

    'form' => [
        'fields' => [
            'name'                     => 'اسم المرحلة',
            'is-won'                   => 'مرحلة الفوز؟',
            'is-won-helper-text'       => 'تُحدد الفرص المنقولة إلى هذه المرحلة كمكسوبة (احتمالية 100%).',
            'requirements'             => 'المتطلبات',
            'requirements-helper-text' => 'إرشادات داخلية حول ما يجب إنجازه قبل نقل فرصة إلى هذه المرحلة.',
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'اسم المرحلة',
            'is-won'        => 'مرحلة الفوز',
            'requirements'  => 'المتطلبات',
            'opportunities' => 'الفرص',
            'created-at'    => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المرحلة',
                    'body'  => 'تم تحديث المرحلة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المرحلة',
                    'body'  => 'تم حذف المرحلة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المراحل',
                    'body'  => 'تم حذف المراحل بنجاح.',
                ],
            ],
        ],
    ],
];
