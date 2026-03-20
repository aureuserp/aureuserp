<?php

return [
    'navigation' => [
        'title' => 'مقالات المدونة',
        'group' => 'الموقع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'title'             => 'العنوان',
                    'sub-title'         => 'العنوان الفرعي',
                    'title-placeholder' => 'عنوان المقال ...',
                    'slug'              => 'الرابط المختصر',
                    'content'           => 'المحتوى',
                    'banner'            => 'البانر',
                ],
            ],

            'seo' => [
                'title' => 'تحسين محركات البحث',

                'fields' => [
                    'meta-title'       => 'عنوان الميتا',
                    'meta-keywords'    => 'كلمات الميتا المفتاحية',
                    'meta-description' => 'وصف الميتا',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'category'     => 'الفئة',
                    'tags'         => 'الوسوم',
                    'name'         => 'الاسم',
                    'color'        => 'اللون',
                    'is-published' => 'منشور',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'العنوان',
            'slug'         => 'الرابط المختصر',
            'author'       => 'الكاتب',
            'category'     => 'الفئة',
            'creator'      => 'أنشئ بواسطة',
            'is-published' => 'منشور',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'category'   => 'الفئة',
            'author'     => 'الكاتب',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-published' => 'منشور',
            'author'       => 'الكاتب',
            'creator'      => 'أنشئ بواسطة',
            'category'     => 'الفئة',
            'tags'         => 'الوسوم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المقال',
                    'body'  => 'تم تحديث المقال بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المقال',
                    'body'  => 'تم استعادة المقال بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المقال',
                    'body'  => 'تم حذف المقال بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المقال نهائياً',
                    'body'  => 'تم حذف المقال نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المقالات',
                    'body'  => 'تم استعادة المقالات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المقالات',
                    'body'  => 'تم حذف المقالات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المقالات نهائياً',
                    'body'  => 'تم حذف المقالات نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'title'   => 'العنوان',
                    'slug'    => 'الرابط المختصر',
                    'content' => 'المحتوى',
                    'banner'  => 'البانر',
                ],
            ],

            'seo' => [
                'title' => 'تحسين محركات البحث',

                'entries' => [
                    'meta-title'       => 'عنوان الميتا',
                    'meta-keywords'    => 'كلمات الميتا المفتاحية',
                    'meta-description' => 'وصف الميتا',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'author'          => 'الكاتب',
                    'created-by'      => 'أنشئ بواسطة',
                    'published-at'    => 'تاريخ النشر',
                    'last-updated-by' => 'آخر تحديث بواسطة',
                    'last-updated'    => 'آخر تحديث في',
                    'created-at'      => 'تاريخ الإنشاء',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'category'     => 'الفئة',
                    'tags'         => 'الوسوم',
                    'name'         => 'الاسم',
                    'color'        => 'اللون',
                    'is-published' => 'منشور',
                ],
            ],
        ],
    ],
];
