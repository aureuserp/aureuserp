<?php

return [
    'navigation' => [
        'title' => 'العملات/الأوراق النقدية',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'                           => 'الاسم',
                    'value'                          => 'قيمة العملة/الورقة النقدية',
                    'is-for-all-configs'             => 'لكل نقاط البيع',
                    'is-for-all-configs-helper-text' => 'إذا تم التحديد، ستكون هذه العملة/الورقة النقدية متاحة في كل نقاط البيع.',
                    'configs'                        => 'نقاط البيع',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'الاسم',
            'value'              => 'قيمة العملة/الورقة النقدية',
            'is-for-all-configs' => 'لكل نقاط البيع',
            'configs'            => 'نقاط البيع',
            'company'            => 'الشركة',
        ],
    ],
];
