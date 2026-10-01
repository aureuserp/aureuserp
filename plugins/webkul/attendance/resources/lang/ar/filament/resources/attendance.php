<?php

return [
    'title' => 'الحضور',

    'navigation' => [
        'title' => 'الحضور',
    ],

    'form' => [
        'employee'                   => 'الموظف',
        'work-date'                  => 'تاريخ العمل',
        'duplicate-day'              => 'لدى :employee سجل حضور بالفعل في :date.',
        'check-in'                   => 'الحضور',
        'check-in-not-in-work-date'  => 'يجب أن يكون الحضور ضمن تاريخ العمل.',
        'check-out'                  => 'الانصراف',
        'check-out-before-check-in'  => 'يجب أن يكون الانصراف بعد الحضور.',
        'check-out-not-in-work-date' => 'يجب أن يكون الانصراف ضمن تاريخ العمل.',
        'long-shift-warning'         => 'تنبيه: هذه الوردية مدتها :hours ساعة.',
        'source'                     => 'المصدر',
        'source-manual'              => 'يدوي',
    ],

    'table' => [
        'columns' => [
            'employee'   => 'الموظف',
            'work-date'  => 'تاريخ العمل',
            'check-in'   => 'الحضور',
            'check-out'  => 'الانصراف',
            'worked'     => 'المدة',
            'source'     => 'المصدر',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'employee'         => 'الموظف',
            'source'           => 'المصدر',
            'work-date'        => 'تاريخ العمل',
            'preset'           => 'فترة سريعة',
            'preset-today'     => 'اليوم',
            'preset-this-week' => 'هذا الأسبوع',
            'preset-this-month'=> 'هذا الشهر',
            'date-from'        => 'من',
            'date-to'          => 'إلى',
        ],

        'toolbar-actions' => [
            'export' => 'تصدير',
        ],

        'record-actions' => [
            'add-checkout' => 'إضافة انصراف',
        ],
    ],
];
