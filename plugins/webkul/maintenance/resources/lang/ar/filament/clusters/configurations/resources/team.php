<?php

return [
    'navigation' => [
        'title' => 'الفرق',
    ],

    'form' => [
        'name'    => 'الاسم',
        'company' => 'الشركة',
        'users'   => 'أعضاء الفريق',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'company'    => 'الشركة',
            'users'      => 'أعضاء الفريق',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفريق',
                    'body'  => 'تم تحديث الفريق بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة الفريق',
                    'body'  => 'تمت استعادة الفريق بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفريق',
                    'body'  => 'تم حذف الفريق بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الفريق نهائياً',
                        'body'  => 'تم حذف الفريق نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف الفريق نهائياً',
                        'body'  => 'الفريق مستخدم ولا يمكن حذفه نهائياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة الفرق',
                    'body'  => 'تمت استعادة الفرق بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفرق',
                    'body'  => 'تم حذف الفرق بنجاح.',
                ],
            ],
        ],
    ],
];
