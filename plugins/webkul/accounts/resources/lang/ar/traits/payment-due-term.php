<?php

return [
    'form' => [
        'value'                  => 'القيمة',
        'due'                    => 'الاستحقاق',
        'delay-due'              => 'تأخير الاستحقاق',
        'delay-type'             => 'نوع التأخير',
        'days-on-the-next-month' => 'أيام في الشهر التالي',
        'days'                   => 'أيام',
        'payment-term'           => 'شرط الدفع',
    ],

    'table' => [
        'columns' => [
            'due'          => 'الاستحقاق',
            'value'        => 'القيمة',
            'value-amount' => 'مبلغ القيمة',
            'after'        => 'بعد',
            'delay-type'   => 'نوع التأخير',
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
];
