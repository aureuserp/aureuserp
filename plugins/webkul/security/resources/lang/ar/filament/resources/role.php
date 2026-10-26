<?php

return [
    'permission-descriptions' => [
        'view_any'         => 'عرض وتصفح جميع السجلات.',
        'view'             => 'عرض سجل واحد بالتفصيل.',
        'create'           => 'إنشاء سجلات جديدة.',
        'update'           => 'تعديل وحفظ السجلات الموجودة.',
        'delete'           => 'حذف سجل واحد.',
        'delete_any'       => 'حذف عدة سجلات دفعة واحدة.',
        'force_delete'     => 'الحذف النهائي لسجل (تجاوز سلة المحذوفات).',
        'force_delete_any' => 'الحذف النهائي لعدة سجلات.',
        'restore'          => 'استعادة سجل مؤرشف.',
        'restore_any'      => 'استعادة عدة سجلات مؤرشفة.',
        'reorder'          => 'تغيير ترتيب السجلات.',
    ],

    'form' => [
        'fields' => [
            'web'     => 'ويب',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'لا يمكن حذف دور النظام',
            'body'  => 'هذا دور نظام ولا يمكن حذفه.',
        ],
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الدور',
                    'body'  => 'تم حذف الدور بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الأدوار',
                    'body'  => 'تم حذف الأدوار المحددة بنجاح.',
                ],
            ],
        ],
    ],
];
