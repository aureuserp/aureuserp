<?php

return [
    'navigation' => [
        'title' => 'الصفحات',
        'group' => 'الموقع الإلكتروني',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'title'             => 'العنوان',
                    'title-placeholder' => 'عنوان الصفحة ...',
                    'slug'              => 'الرابط المختصر',
                    'content'           => 'المحتوى',
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
                    'is-header-visible' => 'مرئي في قائمة الرأس',
                    'is-footer-visible' => 'مرئي في قائمة التذييل',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'             => 'العنوان',
            'slug'              => 'الرابط المختصر',
            'creator'           => 'أنشئ بواسطة',
            'is-published'      => 'منشور',
            'is-header-visible' => 'مرئي في قائمة الرأس',
            'is-footer-visible' => 'مرئي في قائمة التذييل',
            'created-at'        => 'تاريخ الإنشاء',
            'updated-at'        => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-published' => 'منشور',
            'creator'      => 'أنشئ بواسطة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الصفحة',
                    'body'  => 'تم تحديث الصفحة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الصفحة',
                    'body'  => 'تم استعادة الصفحة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الصفحة',
                    'body'  => 'تم حذف الصفحة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الصفحة نهائياً',
                    'body'  => 'تم حذف الصفحة نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الصفحات',
                    'body'  => 'تم استعادة الصفحات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الصفحات',
                    'body'  => 'تم حذف الصفحات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الصفحات نهائياً',
                    'body'  => 'تم حذف الصفحات نهائياً بنجاح.',
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
                    'author'          => 'المؤلف',
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
                    'is-header-visible' => 'مرئي في قائمة الرأس',
                    'is-footer-visible' => 'مرئي في قائمة التذييل',
                ],
            ],
        ],
    ],
];
