<?php

return [
    'heading' => [
        'title' => 'تقويم الصيانة',
    ],

    'config' => [
        'button-text' => [
            'today' => 'اليوم',
            'year'  => 'السنة',
            'month' => 'الشهر',
            'week'  => 'الأسبوع',
            'list'  => 'القائمة',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'طلب جديد',
            'modal-heading' => 'طلب صيانة جديد',
            'notification'  => [
                'success' => [
                    'title' => 'تم إنشاء طلب الصيانة',
                    'body'  => 'تم إنشاء طلب الصيانة بنجاح.',
                ],
                'error' => [
                    'title' => 'تعذر إنشاء طلب الصيانة',
                    'body'  => 'أنشئ مرحلة وفريق صيانة أولاً.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'عرض',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'تعديل',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'الموضوع',
            'scheduled-at' => 'مجدول في',
        ],
    ],

    'infolist' => [
        'title'   => 'طلب الصيانة',
        'entries' => [
            'subject'          => 'الموضوع',
            'date'             => 'التاريخ',
            'time'             => 'الوقت',
            'technician'       => 'الفني',
            'priority'         => 'الأولوية',
            'maintenance-type' => 'نوع الصيانة',
            'stage'            => 'المرحلة',
        ],
    ],
];
