<?php

return [
    'title' => 'التوقعات',

    'navigation' => [
        'title' => 'التوقعات',
    ],

    'kanban' => [
        'filters' => [
            'salesperson' => 'مندوب المبيعات',
            'team'        => 'فريق المبيعات',
            'period'      => 'الإغلاق المتوقع',
            'upcoming'    => 'الإغلاقات القادمة',
            'all-dates'   => 'أي تاريخ',
            'presets'     => 'عوامل التصفية',
            'all'         => 'الكل',
        ],

        'empty-state' => [
            'heading'     => 'لا توجد فرص للتوقع',
            'description' => 'حدد تاريخ إغلاق متوقعاً لفرصك لعرضها هنا.',
        ],

        'notifications' => [
            'moved'        => 'تم تحديث تاريخ الإغلاق المتوقع',
            'unauthorized' => 'غير مسموح لك بتغيير هذه الفرصة.',
        ],
    ],
];
