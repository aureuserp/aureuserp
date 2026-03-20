<?php

return [
    'setup' => [
        'title'               => 'المتابعون',
        'submit-action-title' => 'إضافة متابع',
        'tooltip'             => 'إضافة متابع',

        'form' => [
            'fields' => [
                'recipients'  => 'المستلمون',
                'notify-user' => 'إشعار المستخدم',
                'add-a-note'  => 'إضافة ملاحظة',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تمت إضافة المتابع',
                    'body'  => 'تمت إضافة ":partner" كمتابع.',
                ],

                'error' => [
                    'title' => 'خطأ في إضافة المتابع',
                    'body'  => 'فشل في إضافة ":partner" كمتابع',
                ],
            ],

            'mail' => [
                'subject' => 'دعوة لمتابعة :model: :department',
            ],
        ],
    ],
];
