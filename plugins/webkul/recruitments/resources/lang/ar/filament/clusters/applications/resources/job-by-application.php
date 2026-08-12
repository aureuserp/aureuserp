<?php

return [
    'title' => 'الوظيفة',

    'navigation' => [
        'title' => 'الوظائف',
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'manager-name' => 'المدير',
            'company-name' => 'الشركة',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count طلبات جديدة',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count للتوظيف',
            ],

            'total-application' => [
                'total-application' => ':count طلبات',
            ],
        ],
    ],

];
