<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'قبول الدعوة',
        ],
    ],

    'title' => 'التسجيل',

    'heading' => 'إنشاء حساب',

    'actions' => [

        'login' => [
            'before' => 'أو',
            'label' => 'تسجيل الدخول إلى حسابك',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'البريد الإلكتروني',
        ],

        'name' => [
            'label' => 'الاسم',
        ],

        'password' => [
            'label' => 'كلمة المرور',
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

    'notifications' => [

        'throttled' => [
            'title' => 'محاولات تسجيل كثيرة جداً',
            'body' => 'يرجى المحاولة مرة أخرى بعد :seconds ثانية.',
        ],

    ],

];