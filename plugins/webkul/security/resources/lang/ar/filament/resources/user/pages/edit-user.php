<?php

return [
    'notification' => [
        'title' => 'تم تحديث المستخدم',
        'body'  => 'تم تحديث المستخدم بنجاح.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'تغيير كلمة المرور',

            'notification' => [
                'title' => 'تم تغيير كلمة المرور',
                'body'  => 'تم تغيير كلمة المرور بنجاح.',
            ],

            'form' => [
                'new-password'         => 'كلمة المرور الجديدة',
                'confirm-new-password' => 'تأكيد كلمة المرور الجديدة',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'تم حذف المستخدم',
                'body'  => 'تم حذف المستخدم بنجاح.',
            ],
        ],
    ],
];
