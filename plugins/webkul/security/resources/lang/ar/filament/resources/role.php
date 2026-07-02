<?php

return [
    'form' => [
        'fields' => [
            'web'     => 'ويب',
            'sanctum' => 'Sanctum',
            'select-all-permissions'      => 'تحديد جميع الأذونات',
            'select-all-permissions-hint' => 'منح كل الأذونات لهذا الدور. أوقفه لمسح الكل.',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'لا يمكن حذف دور النظام',
            'body'  => 'هذا دور نظام ولا يمكن حذفه.',
        ],
    ],

    'matrix' => [
        'title'        => 'الأذونات',
        'all-modules'  => 'جميع الإضافات:',
        'select-all'   => 'تحديد الكل',
        'deselect-all' => 'إلغاء تحديد الكل',
        'search'       => 'البحث في الإضافات…',
        'model'        => 'النموذج',
        'action'       => 'تحديد الإجراء',
        'all'          => 'الكل',
        'none'         => 'لا شيء',
        'granted'      => 'ممنوحة',
        'pages'        => 'الصفحات',
        'widgets'      => 'الأدوات',
        'empty'        => 'لا توجد أذونات متاحة.',
    ],
];
