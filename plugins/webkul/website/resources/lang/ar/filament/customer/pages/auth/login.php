<?php

return [
    'title'    => 'تسجيل الدخول',
    'heading'  => 'تسجيل الدخول',
    'messages' => [
        'failed' => 'بيانات الاعتماد هذه لا تتطابق مع سجلاتنا.',
    ],
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
            'label' => 'كلمة المرور',
        ],
        'remember' => [
            'label' => 'تذكرني',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'تسجيل الدخول',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'ليس لديك حساب؟',
            'label'  => 'إنشاء حساب',
        ],
        'request_password_reset' => [
            'label' => 'نسيت كلمة المرور؟',
        ],
    ],
];
