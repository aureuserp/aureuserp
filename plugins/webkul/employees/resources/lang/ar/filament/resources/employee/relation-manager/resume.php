<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'            => 'العنوان',
                'name'             => 'الاسم',
                'type'             => 'النوع',
                'create-type'      => 'إنشاء نوع',
                'duration'         => 'المدة',
                'start-date'       => 'تاريخ البداية',
                'end-date'         => 'تاريخ النهاية',
                'display-type'     => 'نوع العرض',
                'description'      => 'الوصف',
                'attachments'      => 'المرفقات',
                'file'             => 'الملف',
                'file-helper-text' => 'الصيغ المقبولة: PDF و DOC و DOCX و TXT و PNG و JPEG و WEBP. الحد الأقصى 10 ميجابايت لكل ملف.',
                'attachment-name'  => 'التسمية',
                'add-attachment'   => 'إضافة مرفق',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'العنوان',
            'start-date'   => 'تاريخ البداية',
            'end-date'     => 'تاريخ النهاية',
            'display-type' => 'نوع العرض',
            'description'  => 'الوصف',
            'created-by'   => 'أنشئ بواسطة',
            'attachments'  => 'المرفقات',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'group-by-type'         => 'تجميع حسب النوع',
            'group-by-display-type' => 'تجميع حسب نوع العرض',
        ],

        'header-actions' => [
            'add-resume' => 'إضافة سيرة ذاتية',
        ],

        'filters' => [
            'type'            => 'النوع',
            'start-date-from' => 'تاريخ البداية من',
            'start-date-to'   => 'تاريخ البداية إلى',
            'created-from'    => 'أنشئ من',
            'created-to'      => 'أنشئ إلى',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث السيرة الذاتية',
                    'body'  => 'تم تحديث السيرة الذاتية بنجاح.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء السيرة الذاتية',
                    'body'  => 'تم إنشاء السيرة الذاتية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السيرة الذاتية',
                    'body'  => 'تم حذف السيرة الذاتية بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السير الذاتية',
                    'body'  => 'تم حذف السير الذاتية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'           => 'العنوان',
            'display-type'    => 'نوع العرض',
            'type'            => 'النوع',
            'description'     => 'الوصف',
            'duration'        => 'المدة',
            'start-date'      => 'تاريخ البداية',
            'end-date'        => 'تاريخ النهاية',
            'attachments'     => 'المرفقات',
            'file'            => 'الملف',
            'attachment-name' => 'التسمية',
        ],
    ],
];
