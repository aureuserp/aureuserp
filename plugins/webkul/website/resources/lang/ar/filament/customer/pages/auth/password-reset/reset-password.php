<?php

return [
    'title'         => 'إعادة تعيين كلمة المرور',
    'heading'       => 'إعادة تعيين كلمة المرور',
    'notifications' => [
        'throttled' => [
            'title' => 'محاولات كثيرة جداً. حاول مرة أخرى بعد :seconds ثانية.',
            'body'  => 'يرجى الانتظار :seconds ثانية (:minutes دقيقة) قبل المحاولة مرة أخرى.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'البريد الإلكتروني',
        ],
        'password' => [
            'label'                => 'كلمة المرور الجديدة',
            'validation_attribute' => 'كلمة المرور',
        ],
        'password_confirmation' => [
            'label' => 'تأكيد كلمة المرور الجديدة',
        ],
        'actions' => [
            'reset' => [
                'label' => 'إعادة تعيين كلمة المرور',
            ],
        ],
    ],
];
