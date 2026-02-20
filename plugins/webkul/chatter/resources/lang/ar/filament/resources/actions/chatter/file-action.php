<?php

return [
    'setup' => [
        'title'   => 'المرفقات',
        'tooltip' => 'رفع المرفقات',

        'form' => [
            'fields' => [
                'files'                  => 'الملفات',
                'attachment-helper-text' => 'الحد الأقصى لحجم الملف: 10 ميجابايت. الأنواع المسموحة: صور، PDF، Word، Excel، نص',

                'actions' => [
                    'delete' => [
                        'title' => 'تم حذف الملف',
                        'body'  => 'تم حذف الملف بنجاح.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تم رفع المرفقات',
                    'body'  => 'تم رفع المرفقات بنجاح.',
                ],

                'warning'  => [
                    'title' => 'لا توجد ملفات جديدة',
                    'body'  => 'تم رفع جميع الملفات بالفعل.',
                ],

                'error' => [
                    'title' => 'خطأ في رفع المرفقات',
                    'body'  => 'فشل في رفع المرفقات',
                ],
            ],
        ],
    ],
];
