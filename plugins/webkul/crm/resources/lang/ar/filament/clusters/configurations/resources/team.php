<?php

return [
    'model-label'        => 'فريق المبيعات',
    'plural-model-label' => 'فرق المبيعات',

    'navigation' => [
        'title' => 'فرق المبيعات',
    ],

    'pages' => [
        'assignment' => [
            'navigation' => 'قواعد التعيين',
            'title'      => ':team — قواعد التعيين',
        ],

        'alias' => [
            'navigation' => 'عنوان البريد البديل',
            'title'      => ':team — عنوان البريد البديل',
        ],

        'members' => [
            'title'              => ':team — الأعضاء',
            'inactive'           => 'غير نشط',
            'skipped'            => 'يتخطى التعيين التلقائي',
            'rules'              => '{1} :count قاعدة|[0,*] :count قواعد',
            'lead-limit'         => 'حد العملاء المحتملين (30 يومًا): :assigned / :max',
            'lead-limit-tooltip' => 'تم تعيين :assigned من :max عميل محتمل في آخر 30 يومًا',
        ],
    ],
];
