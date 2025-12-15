<?php

return [
    'title'         => 'نسيت كلمة المرور',
    'heading'       => 'نسيت كلمة المرور',
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
        'actions' => [
            'request' => [
                'label' => 'إرسال رابط إعادة التعيين',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'العودة لتسجيل الدخول',
        ],
    ],
];
