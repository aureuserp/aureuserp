<?php

return [
    'create-employee' => 'إنشاء موظف',
    'goto-employee'   => 'الذهاب إلى الموظف',

    'notification' => [
        'title' => 'تم تحديث المتقدم',
        'body'  => 'تم تحديث المتقدم بنجاح.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'تم حذف المتقدم',
                'body'  => 'تم حذف المتقدم بنجاح.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'تم حذف المتقدم',
                'body'  => 'تم حذف المتقدم نهائياً بنجاح.',
            ],
        ],

        'refuse' => [
            'title'        => 'سبب الرفض',
            'notification' => [
                'title' => 'تم رفض المتقدم',
                'body'  => 'تم رفض المتقدم بنجاح.',
            ],
        ],

        'reopen' => [
            'title'        => 'إعادة فتح المتقدم',
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

        'application-confirm' => [
            'subject' => 'طلب التوظيف الخاص بك: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'تم تعيينك للمتقدم :applicant.',
        ],
    ],
];
