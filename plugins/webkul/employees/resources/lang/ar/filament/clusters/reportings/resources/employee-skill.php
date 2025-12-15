<?php

return [
    'title' => 'المهارات',

    'navigation' => [
        'title' => 'المهارات',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'تفاصيل المهارة',

                'fields' => [
                    'employee'       => 'الموظف',
                    'skill'          => 'المهارة',
                    'skill-level'    => 'المستوى',
                    'skill-type'     => 'نوع المهارة',
                ],
            ],
            'addition-information' => [
                'title' => 'معلومات إضافية',

                'fields' => [
                    'created-by' => 'أنشئ بواسطة',
                    'updated-by' => 'حُدث بواسطة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'المعرف',
            'employee'        => 'الموظف',
            'skill'           => 'المهارة',
            'skill-level'     => 'المستوى',
            'skill-type'      => 'نوع المهارة',
            'user'            => 'المستخدم',
            'proficiency'     => 'الإتقان',
            'created-by'      => 'أنشئ بواسطة',
            'created-at'      => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'employee'        => 'الموظف',
            'skill'           => 'المهارة',
            'skill-level'     => 'المستوى',
            'skill-type'      => 'نوع المهارة',
            'user'            => 'المستخدم',
            'created-by'      => 'أنشئ بواسطة',
            'created-at'      => 'تاريخ الإنشاء',
            'updated-at'      => 'تاريخ التحديث',
        ],

        'groups' => [
            'employee'   => 'الموظف',
            'skill-type' => 'نوع المهارة',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'تفاصيل المهارة',

                'entries' => [
                    'employee'        => 'الموظف',
                    'skill'           => 'المهارة',
                    'skill-level'     => 'المستوى',
                    'skill-type'      => 'نوع المهارة',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'entries' => [
                    'created-by' => 'أنشئ بواسطة',
                    'updated-by' => 'حُدث بواسطة',
                ],
            ],
        ],
    ],
];
