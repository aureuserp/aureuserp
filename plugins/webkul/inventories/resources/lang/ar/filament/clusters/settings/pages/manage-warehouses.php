<?php

return [
    'title' => 'إدارة المستودعات',

    'form' => [
        'enable-locations'                      => 'المواقع',
        'enable-locations-helper-text'          => 'تتبع موقع المنتج في المستودع',
        'configure-locations'                   => 'إعداد المواقع',
        'enable-multi-steps-routes'             => 'مسارات متعددة الخطوات',
        'enable-multi-steps-routes-helper-text' => 'استخدم مساراتك الخاصة لإدارة نقل المنتجات بين المستودعات',
        'configure-routes'                      => 'إعداد مسارات المستودع',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'لديك عدة مستودعات',
                'body'  => 'لا يمكنك إلغاء تنشيط المواقع المتعددة إذا كان لديك أكثر من مستودع.',
            ],
        ],
    ],
];
