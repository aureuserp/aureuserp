<?php

return [
    'navigation' => [
        'title' => 'المواقع',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'location'                     => 'الموقع',
                    'location-placeholder'         => 'مثال: مخزون احتياطي',
                    'parent-location'              => 'الموقع الأصلي',
                    'parent-location-hint-tooltip' => 'الموقع الرئيسي الذي يشمل هذا الموقع. على سبيل المثال، "منطقة الإرسال" هي جزء من الموقع الأصلي "البوابة 1".',
                    'external-notes'               => 'ملاحظات خارجية',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',

                'fields' => [
                    'location-type'                 => 'نوع الموقع',
                    'company'                       => 'الشركة',
                    'storage-category'              => 'فئة التخزين',
                    'is-scrap'                      => 'موقع خردة؟',
                    'is-scrap-hint-tooltip'         => 'حدد هذا الخيار لتعيين هذا الموقع لتخزين البضائع التالفة أو المرفوضة.',
                    'is-dock'                       => 'موقع رصيف؟',
                    'is-dock-hint-tooltip'          => 'حدد هذا الخيار لتعيين هذا الموقع لتخزين البضائع الجاهزة للشحن.',
                    'is-replenish'                  => 'موقع تجديد؟',
                    'is-replenish-hint-tooltip'     => 'فعّل هذه الوظيفة لاسترداد جميع الكميات المطلوبة للتجديد في هذا الموقع.',
                    'logistics'                     => 'اللوجستيات',
                    'removal-strategy'              => 'استراتيجية السحب',
                    'removal-strategy-hint-tooltip' => 'تحدد الطريقة الافتراضية لتحديد الرف والدفعة والموقع الدقيق لسحب المنتجات. يمكن فرض هذه الطريقة على مستوى فئة المنتج، مع الرجوع إلى المواقع الأصلية إذا لم يتم تعيينها هنا.',
                    'cyclic-counting'               => 'الجرد الدوري',
                    'inventory-frequency'           => 'تكرار الجرد',
                    'last-inventory'                => 'آخر جرد',
                    'last-inventory-hint-tooltip'   => 'تاريخ آخر جرد في هذا الموقع.',
                    'next-expected'                 => 'المتوقع التالي',
                    'next-expected-hint-tooltip'    => 'تاريخ الجرد المخطط التالي بناءً على الجدول الدوري.',
                ],
            ],

            'additional' => [
                'title'  => 'معلومات إضافية',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'الموقع',
            'type'             => 'النوع',
            'storage-category' => 'فئة التخزين',
            'company'          => 'الشركة',
            'deleted-at'       => 'تاريخ الحذف',
            'created-at'       => 'تاريخ الإنشاء',
            'updated-at'       => 'تاريخ التحديث',
        ],

        'groups' => [
            'warehouse'       => 'المستودع',
            'type'            => 'النوع',
            'created-at'      => 'تاريخ الإنشاء',
            'updated-at'      => 'تاريخ التحديث',
        ],

        'filters' => [
            'location' => 'الموقع',
            'type'     => 'النوع',
            'company'  => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الموقع',
                    'body'  => 'تم تحديث الموقع بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الموقع',
                    'body'  => 'تم استعادة الموقع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الموقع',
                    'body'  => 'تم حذف الموقع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الموقع نهائياً',
                        'body'  => 'تم حذف الموقع نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الموقع',
                        'body'  => 'لا يمكن حذف الموقع لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'طباعة الباركود',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المواقع',
                    'body'  => 'تم استعادة المواقع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المواقع',
                    'body'  => 'تم حذف المواقع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المواقع نهائياً',
                        'body'  => 'تم حذف المواقع نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المواقع',
                        'body'  => 'لا يمكن حذف المواقع لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'entries' => [
                    'location'                     => 'الموقع',
                    'location-placeholder'         => 'مثال: مخزون احتياطي',
                    'parent-location'              => 'الموقع الأصلي',
                    'parent-location-hint-tooltip' => 'الموقع الرئيسي الذي يشمل هذا الموقع. على سبيل المثال، "منطقة الإرسال" هي جزء من الموقع الأصلي "البوابة 1".',
                    'external-notes'               => 'ملاحظات خارجية',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',

                'entries' => [
                    'location-type'                 => 'نوع الموقع',
                    'company'                       => 'الشركة',
                    'storage-category'              => 'فئة التخزين',
                    'is-scrap'                      => 'موقع خردة؟',
                    'is-scrap-hint-tooltip'         => 'حدد هذا الخيار لتعيين هذا الموقع لتخزين البضائع التالفة أو المرفوضة.',
                    'is-dock'                       => 'موقع رصيف؟',
                    'is-dock-hint-tooltip'          => 'حدد هذا الخيار لتعيين هذا الموقع لتخزين البضائع الجاهزة للشحن.',
                    'is-replenish'                  => 'موقع تجديد؟',
                    'is-replenish-hint-tooltip'     => 'فعّل هذه الوظيفة لاسترداد جميع الكميات المطلوبة للتجديد في هذا الموقع.',
                    'logistics'                     => 'اللوجستيات',
                    'removal-strategy'              => 'استراتيجية السحب',
                    'removal-strategy-hint-tooltip' => 'تحدد الطريقة الافتراضية لتحديد الرف والدفعة والموقع الدقيق لسحب المنتجات. يمكن فرض هذه الطريقة على مستوى فئة المنتج، مع الرجوع إلى المواقع الأصلية إذا لم يتم تعيينها هنا.',
                    'cyclic-counting'               => 'الجرد الدوري',
                    'inventory-frequency'           => 'تكرار الجرد',
                    'last-inventory'                => 'آخر جرد',
                    'last-inventory-hint-tooltip'   => 'تاريخ آخر جرد في هذا الموقع.',
                    'next-expected'                 => 'المتوقع التالي',
                    'next-expected-hint-tooltip'    => 'تاريخ الجرد المخطط التالي بناءً على الجدول الدوري.',
                ],
            ],

            'additional' => [
                'title'  => 'معلومات إضافية',
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-by'   => 'أنشئ بواسطة',
                    'created-at'   => 'تاريخ الإنشاء',
                    'last-updated' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
