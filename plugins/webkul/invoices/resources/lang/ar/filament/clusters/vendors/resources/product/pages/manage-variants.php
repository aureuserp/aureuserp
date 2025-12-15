<?php

return [
    'title' => 'المتغيرات',

    'form' => [
        'date'                   => 'التاريخ',
        'employee'               => 'الموظف',
        'description'            => 'الوصف',
        'time-spent'             => 'الوقت المستغرق',
        'time-spent-helper-text' => 'الوقت المستغرق بالساعات (مثال: 1.5 ساعة تعني ساعة و30 دقيقة)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'التاريخ',
            'employee'               => 'الموظف',
            'description'            => 'الوصف',
            'time-spent'             => 'الوقت المستغرق',
            'time-spent-on-subtasks' => 'الوقت المستغرق في المهام الفرعية',
            'total-time-spent'       => 'إجمالي الوقت المستغرق',
            'remaining-time'         => 'الوقت المتبقي',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المتغير',
                    'body'  => 'تم حذف المتغير بنجاح.',
                ],
            ],
        ],
    ],
];
