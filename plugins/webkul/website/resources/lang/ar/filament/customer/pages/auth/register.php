<?php

return [
    'title'         => 'إنشاء حساب',
    'heading'       => 'إنشاء حساب',
    'notifications' => [
        'throttled' => [
            'title' => 'محاولات كثيرة جداً. حاول مرة أخرى بعد :seconds ثانية.',
            'body'  => 'يرجى الانتظار :seconds ثانية (:minutes دقيقة) قبل المحاولة مرة أخرى.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'الاسم',
        ],
        'email' => [
            'label' => 'البريد الإلكتروني',
        ],
        'password' => [
            'label'                => 'كلمة المرور',
            'validation_attribute' => 'كلمة المرور',
        ],
        'password_confirmation' => [
            'label' => 'تأكيد كلمة المرور',
        ],
        'actions' => [
            'register' => [
                'label' => 'إنشاء حساب',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'before' => 'لديك حساب بالفعل؟',
            'label'  => 'تسجيل الدخول',
        ],
    ],
];
