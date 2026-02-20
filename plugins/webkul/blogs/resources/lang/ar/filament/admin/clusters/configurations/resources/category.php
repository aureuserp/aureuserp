<?php

return [
    'navigation' => [
        'title' => 'الفئات',
        'group' => 'المدونة',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'name-placeholder' => 'عنوان الفئة ...',
            'sub-title'        => 'العنوان الفرعي',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'sub-title'  => 'العنوان الفرعي',
            'posts'      => 'المقالات',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-published' => 'منشور',
            'author'       => 'الكاتب',
            'creator'      => 'أنشئ بواسطة',
            'category'     => 'الفئة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفئة',
                    'body'  => 'تم تحديث الفئة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفئة',
                    'body'  => 'تم استعادة الفئة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئة',
                    'body'  => 'تم حذف الفئة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الفئة نهائياً',
                        'body'  => 'تم حذف الفئة نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف الفئة',
                        'body'  => 'لا يمكن حذف الفئة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'لا يمكن حذف الفئة',
                    'body' => 'لا يمكنك حذف هذه الفئة لأنها مرتبطة ببعض المقالات.',
                ],

                'exception' => 'لا يمكنك حذف هذه الفئة نهائياً لأنها مرتبطة ببعض المقالات.'
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفئات',
                    'body'  => 'تم استعادة الفئات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئات',
                    'body'  => 'تم حذف الفئات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئات نهائياً',
                    'body'  => 'تم حذف الفئات نهائياً بنجاح.',
                ],
            ],
            
            'force-delete-error' => [
                'notification' => [
                    'title' => 'لا يمكن حذف الفئة',
                    'body' => 'لا يمكنك حذف هذه الفئة لأنها مرتبطة ببعض المقالات.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
