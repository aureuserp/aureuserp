<?php

return [
    'heading' => ':report - حتى :date',
    'as-of'   => 'حتى :date',

    'table' => [
        'columns' => [
            'at-date'      => 'غير مستحق',
            'period-range' => ':from-:to',
            'older'        => 'أقدم',
            'older-than'   => ':days+',
        ],
    ],

    'totals' => [
        'total' => 'إجمالي أعمار الذمم المدينة',
    ],

    'messages' => [
        'unposted-entries' => 'توجد قيود يومية غير مرحلة قبل هذه الفترة أو ضمنها.',
        'empty'            => 'لم يتم العثور على ذمم مدينة للمعايير المحددة.',
    ],

    'footer' => [
        'generated-on' => 'تم الإنشاء في :date',
    ],
];
