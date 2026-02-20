<?php

return [
    'navigation' => [
        'title' => 'الإضافات',
    ],

    'tabs' => [
        'apps'          => 'التطبيقات',
        'extra'         => 'إضافي',
        'installed'     => 'مثبت',
        'not-installed' => 'غير مثبت',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'مزامنة الإضافات المتاحة',
            'modal-heading'             => 'مزامنة الإضافات',
            'modal-description'         => 'سيؤدي هذا إلى فحص وتسجيل أي إضافات جديدة موجودة.',
            'modal-submit-action-label' => 'مزامنة الإضافات',

            'notification' => [
                'success' => [
                    'title' => 'تمت مزامنة الإضافات بنجاح',
                    'body'  => 'تم العثور على ومزامنة إضافة :count جديدة.',
                ],

                'error' => [
                    'title' => 'فشلت مزامنة الإضافة',
                    'body'  => 'حدث خطأ (:error) أثناء مزامنة الإضافات. يرجى المحاولة مرة أخرى.',
                ],
            ],
        ],
    ],
];
