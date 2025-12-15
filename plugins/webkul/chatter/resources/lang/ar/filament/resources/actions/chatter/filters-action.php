<?php

return [
    'tooltip' => 'التصفيات',

    'fields'  => [
        'search'             => 'البحث',
        'search-placeholder' => 'البحث في الرسائل...',
        'type'               => 'النوع',
        'date'               => 'التاريخ',
        'sort-by'            => 'ترتيب حسب',
        'pinned-only'        => 'المثبتة فقط',
    ],
    'type-options' => [
        'all'          => 'جميع الأنواع',
        'note'         => 'الملاحظات',
        'comment'      => 'التعليقات',
        'notification' => 'الإشعارات',
        'activity'     => 'الأنشطة',
    ],
    'date-options' => [
        ''          => 'أي وقت',
        'today'     => 'اليوم',
        'yesterday' => 'أمس',
        'week'      => 'آخر 7 أيام',
        'month'     => 'آخر 30 يوم',
        'quarter'   => 'آخر 3 أشهر',
        'year'      => 'السنة الماضية',
    ],
    'sort-options' => [
        'created_at_desc' => 'الأحدث أولاً',
        'created_at_asc'  => 'الأقدم أولاً',
        'updated_at_desc' => 'المُحدَّث مؤخراً',
        'priority'        => 'الأولوية',
    ],
    'actions' => [
        'apply' => 'تطبيق التصفيات',
    ],
];
