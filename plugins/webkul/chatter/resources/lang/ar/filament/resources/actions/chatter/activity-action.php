<?php

return [
    'setup' => [
        'title'               => 'جدولة نشاط',
        'submit-action-title' => 'جدولة',

        'form' => [
            'fields' => [
                'activity-plan' => 'خطة النشاط',
                'plan-date'     => 'تاريخ الخطة',
                'plan-summary'  => 'ملخص الخطة',
                'activity-type' => 'نوع النشاط',
                'due-date'      => 'تاريخ الاستحقاق',
                'summary'       => 'الملخص',
                'assigned-to'   => 'مُعيَّن إلى',
                'log-note'      => 'ملاحظة السجل',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تم إنشاء النشاط',
                    'body'  => 'تم إنشاء النشاط.',
                ],

                'warning'  => [
                    'title' => 'لا توجد ملفات جديدة',
                    'body'  => 'تم رفع جميع الملفات بالفعل.',
                ],

                'error' => [
                    'title' => 'فشل إنشاء النشاط',
                    'body'  => 'فشل في إنشاء النشاط',
                ],
            ],
        ],
    ],
];
