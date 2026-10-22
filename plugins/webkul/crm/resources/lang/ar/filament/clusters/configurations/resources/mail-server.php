<?php

return [
    'model-label'        => 'خادم البريد الوارد',
    'plural-model-label' => 'خوادم البريد الوارد',

    'navigation' => [
        'title' => 'خوادم البريد الوارد',
        'group' => 'البريد الإلكتروني',
    ],

    'header-actions' => [
        'create' => [
            'label'        => 'خادم بريد جديد',
            'notification' => 'تم إنشاء خادم البريد',
        ],
    ],

    'form' => [
        'name'                        => 'الاسم',
        'name-placeholder'            => 'مثل صندوق المبيعات',
        'host'                        => 'الخادم (مضيف IMAP)',
        'encryption'                  => 'التشفير',
        'port'                        => 'المنفذ',
        'folder'                      => 'المجلد',
        'username'                    => 'اسم المستخدم',
        'password'                    => 'كلمة المرور',
        'password-helper'             => 'اتركه فارغًا للإبقاء على كلمة المرور الحالية.',
        'validate-certificate'        => 'التحقق من شهادة SSL',
        'validate-certificate-helper' => 'أوقفه فقط للخوادم ذات الشهادة الموقعة ذاتيًا.',
        'active'                      => 'نشط',
        'active-helper'               => 'يُفحص كل دقيقة بحثًا عن رسائل جديدة.',

        'encryptions' => [
            'ssl'  => 'SSL/TLS (المنفذ 993)',
            'tls'  => 'STARTTLS (المنفذ 143)',
            'none' => 'بدون (المنفذ 143)',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'host'         => 'الخادم',
            'folder'       => 'المجلد',
            'active'       => 'نشط',
            'last-fetched' => 'آخر فحص',
            'last-error'   => 'آخر خطأ',
        ],

        'actions' => [
            'test' => [
                'label'   => 'اختبار الاتصال',
                'success' => 'تم الاتصال بنجاح: :count رسالة في المجلد.',
                'failure' => 'فشل الاتصال',
            ],

            'fetch' => [
                'label'        => 'جلب الآن',
                'queued'       => 'جارٍ جلب الرسائل',
                'queued-body'  => 'تُقرأ الرسائل الجديدة في الخلفية (يجب تشغيل عامل قائمة الانتظار). راجع النتيجة في الرسائل المستلمة.',
            ],

            'edit' => [
                'notification' => 'تم تحديث خادم البريد',
            ],

            'delete' => [
                'notification' => 'تم حذف خادم البريد',
            ],
        ],

        'empty' => [
            'heading'     => 'لا يوجد خادم بريد وارد',
            'description' => 'أضف صندوق البريد الذي يستقبل الرسائل المرسلة إلى الأسماء المستعارة لفرق المبيعات.',
        ],
    ],
];
