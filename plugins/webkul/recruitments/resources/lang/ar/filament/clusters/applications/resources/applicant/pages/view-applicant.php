<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'تم حذف المتقدم',
                'body'  => 'تم حذف المتقدم بنجاح.',
            ],
        ],

        'refuse' => [
            'title'        => 'سبب الرفض',
            'form'         => [
                'fields' => [
                    'notify'   => 'إشعار',
                    'email-to' => 'إرسال البريد إلى',
                ],
            ],
            'notification' => [
                'title' => 'تم رفض المتقدم',
                'body'  => 'تم رفض المتقدم بنجاح.',
            ],
        ],

        'reopen' => [
            'title'        => 'استعادة المتقدم من الرفض',
            'notification' => [
                'title' => 'تم إعادة فتح المتقدم',
                'body'  => 'تم إعادة فتح المتقدم بنجاح.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'تم تحديث حالة المتقدم',
                'body'  => 'تم تحديث حالة المتقدم بنجاح.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'طلب التوظيف الخاص بك: :application',
        ],
    ],
];
