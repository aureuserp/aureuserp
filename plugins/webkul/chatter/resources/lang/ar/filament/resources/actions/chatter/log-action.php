<?php

return [
    'setup' => [
        'title'        => 'ملاحظة السجل',
        'submit-title' => 'تسجيل',

        'form' => [
            'fields' => [
                'hide-subject'            => 'إخفاء الموضوع',
                'add-subject'             => 'إضافة موضوع',
                'subject'                 => 'الموضوع',
                'write-message-here'      => 'اكتب رسالتك هنا',
                'attachments-helper-text' => 'الحد الأقصى لحجم الملف: 10 ميجابايت. الأنواع المسموحة: صور، PDF، Word، Excel، نص',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تمت إضافة ملاحظة السجل',
                    'body'  => 'تمت إضافة ملاحظة السجل بنجاح.',
                ],

                'error' => [
                    'title' => 'خطأ في إضافة السجل',
                    'body'  => 'فشل في إضافة ملاحظة السجل',
                ],
            ],
        ],
    ],
];
