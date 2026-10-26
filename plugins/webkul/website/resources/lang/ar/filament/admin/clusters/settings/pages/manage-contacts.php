<?php

return [
    'title' => 'إدارة جهات الاتصال',

    'form' => [
        'sections' => [
            'contact-form' => [
                'title'       => 'اتصل بنا',
                'description' => 'نموذج اتصل بنا في الموقع وما يحدث عند إرسال الزائر له.',

                'fields' => [
                    'enabled'        => 'تفعيل اتصل بنا',
                    'enabled-helper' => 'يعرض صفحة اتصل بنا ورابطها في قائمة الموقع.',
                    'action'         => 'بعد الإرسال',
                    'email-helper'   => 'تُرسل الرسالة بالبريد إلى :email.',
                    'crm-helper'     => 'تنشئ الرسالة سجلًا في إدارة علاقات العملاء ببيانات الزائر.',
                ],

                'actions' => [
                    'email'       => 'إرسال بريد إلكتروني',
                    'lead'        => 'إنشاء عميل محتمل',
                    'opportunity' => 'إنشاء فرصة',
                ],
            ],

            'contacts' => [
                'title' => 'جهات الاتصال',

                'fields' => [
                    'email' => 'البريد الإلكتروني',
                    'phone' => 'الهاتف',
                ],
            ],

            'social-links' => [
                'title' => 'روابط التواصل الاجتماعي',

                'fields' => [
                    'twitter'   => 'تويتر',
                    'facebook'  => 'فيسبوك',
                    'instagram' => 'إنستغرام',
                    'linkedin'  => 'لينكد إن',
                    'pinterest' => 'بنترست',
                    'tiktok'    => 'تيك توك',
                    'github'    => 'غيت هب',
                    'slack'     => 'سلاك',
                    'whatsapp'  => 'واتساب',
                    'youtube'   => 'يوتيوب',
                ],
            ],
        ],
    ],
];
