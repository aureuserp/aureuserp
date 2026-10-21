<?php

return [
    'navigation' => [
        'title' => 'نماذج الملاحظات',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'  => 'ملاحظة',
                    'color' => 'اللون',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'ملاحظة',
            'color'   => 'اللون',
            'company' => 'الشركة',
        ],
    ],
];
