<?php

return [
    'title'        => 'الأصل',
    'plural-title' => 'الأصول',

    'navigation' => [
        'title' => 'الأصول',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'تفاصيل الأصل',
                'fields' => [
                    'name'                       => 'اسم الأصل',
                    'product'                    => 'المنتج / الموديل',
                    'product-type'               => 'نوع المنتج',
                    'quantity'                   => 'الكمية',
                    'quantity-help'              => 'إجمالي الكمية المستلمة في هذه الدفعة.',
                    'asset-tag'                  => 'رمز الأصل',
                    'asset-tag-help'             => 'رمز تعريفي فريد للأصل داخل الشركة.',
                    'asset-tag-auto-placeholder' => 'AST-XXXXX (توليد تلقائي)',
                    'serial-no'                  => 'الرقم التسلسلي',
                    'model'                      => 'الموديل',
                    'category'                   => 'الفئة',
                    'condition'                  => 'الحالة الفنية',
                    'cost'                       => 'التكلفة',
                    'purchase-date'              => 'تاريخ الشراء',
                    'warranty-until'             => 'الضمان حتى',
                    'warehouse'                  => 'المخزن / المستودع',
                    'warehouse-help'             => 'المخزن الفعلي في نظام المخزون الذي يُحفظ فيه الأصل عندما يكون متاحاً.',
                    'initial-placement'          => 'التوجيه المباشر الأولي',
                    'placements'                 => [
                        'warehouse'        => 'إيداع في مخزن أصول',
                        'site-asset'       => 'تركيب في موقع أصول ثابتة',
                        'employee-custody' => 'تسليم كعهدة موظف',
                    ],
                    'current-site'               => 'الموقع',
                    'current-employee'           => 'الموظف',
                    'note'                       => 'ملاحظات',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'company' => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'asset-tag'        => 'رمز الأصل',
            'name'             => 'اسم الأصل',
            'product'          => 'المنتج',
            'product-type'     => 'نوع المنتج',
            'quantity'         => 'الكمية',
            'model'            => 'الموديل',
            'serial-no'        => 'الرقم التسلسلي',
            'category'         => 'الفئة',
            'status'           => 'حالة الأصل',
            'condition'        => 'الحالة الفنية',
            'holder'           => 'العهدة الحالية',
            'warehouse'        => 'المخزن / المستودع',
            'current-employee' => 'الموظف الحالي',
            'current-site'     => 'موقع العهدة الحالي',
            'cost'             => 'التكلفة',
            'company'          => 'الشركة',
            'deleted-at'       => 'تاريخ الأرشفة',
            'created-at'       => 'تاريخ الإنشاء',
            'updated-at'       => 'تاريخ التحديث',
        ],

        'groups' => [
            'product'          => 'المنتج / الموديل',
            'category'         => 'الفئة',
            'status'           => 'حالة الأصل',
            'condition'        => 'الحالة الفنية',
            'warehouse'        => 'المخزن / المستودع',
            'current-employee' => 'الموظف الحالي',
            'current-site'     => 'موقع العهدة الحالي',
        ],

        'filters' => [
            'status'              => 'حالة الأصل',
            'condition'           => 'الحالة الفنية',
            'category'            => 'الفئة',
            'current-holder-type' => 'نوع العهدة',
            'current-employee'    => 'الموظف الحالي',
            'department'          => 'القسم',
            'warehouse'           => 'المخزن / المستودع',
            'current-site'        => 'موقع العهدة الحالي',
            'site-usage-type'     => 'طبيعة استخدام الموقع',
            'site-type'           => 'نوع موقع العهدة',
            'product'             => 'المنتج',
            'product-type'        => 'نوع المنتج',
            'company'             => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الأصل',
                    'body'  => 'تم تحديث بيانات الأصل بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'تعذر حذف الأصل',
                    ],
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'تصدير',
            ],
        ],
    ],

    'actions' => [
        'issue' => [
            'label' => 'تسليم عهدة',
            'modal' => [
                'heading'                => 'تسليم عهدة أصل',
                'description'            => 'تسليم الأصل لموظف أو موقع.',
                'consumable-description' => 'الرصيد المتاح: :quantity وحدة. تُخصم الكميات من هذه الدفعة عند الصرف.',
                'submit'                 => 'تأكيد التسليم',
            ],
            'fields' => [
                'allocation-mode'    => 'خيار التخصيص',
                'allocation-modes'   => [
                    'employee-custody' => 'عهدة موظف',
                    'site-asset'       => 'أصل موقع ثابت',
                ],
                'holder-type'        => 'نوع العهدة',
                'employee'           => 'الموظف',
                'employees'          => 'المستلمون',
                'departments'        => 'الأقسام',
                'departments-help'   => 'اختيار جماعي للمستلمين حسب القسم. تحديد الموقع أو الأقسام يعبئ قائمة المستلمين تلقائياً.',
                'recipients-summary' => ':count مستلم — إجمالي :total وحدة',
                'site'               => 'الموقع',
                'receiver'           => 'المستلم في الميدان',
                'condition-out'      => 'الحالة الفنية عند التسليم',
                'quantity'           => 'الكمية لكل مستلم',
                'quantity-help'      => 'يحصل كل مستلم محدد على هذه الكمية، ويُخصم الإجمالي من رصيد الدفعة.',
                'issued-at'          => 'تاريخ ووقت التسليم',
                'expected-return-at' => 'تاريخ الإرجاع المتوقع',
                'issue-note'         => 'ملاحظة التسليم',
                'issue-attachments'  => 'مرفقات التسليم',
            ],
            'notification' => [
                'title' => 'تم تسليم العهدة',
                'body'  => 'تم تسليم عهدة الأصل بنجاح.',
            ],
        ],

        'transfer' => [
            'label' => 'نقل عهدة',
            'modal' => [
                'heading'     => 'نقل عهدة أصل',
                'description' => 'نقل الأصل إلى موظف أو موقع جديد.',
                'submit'      => 'تأكيد النقل',
            ],
            'fields' => [
                'allocation-mode'    => 'خيار التخصيص',
                'allocation-modes'   => [
                    'employee-custody' => 'عهدة موظف',
                    'site-asset'       => 'أصل موقع ثابت',
                ],
                'holder-type'        => 'نوع العهدة الجديد',
                'employee'           => 'الموظف الجديد',
                'site'               => 'الموقع الجديد',
                'receiver'           => 'المستلم في الميدان',
                'condition-in'       => 'الحالة من الحائز السابق',
                'issued-at'          => 'تاريخ ووقت النقل',
                'expected-return-at' => 'تاريخ الإرجاع المتوقع',
                'issue-note'         => 'ملاحظة النقل',
                'return-note'        => 'ملاحظة الإرجاع من الحائز السابق',
                'return-attachments' => 'مرفقات الاستلام السابقة',
                'issue-attachments'  => 'مرفقات التسليم الجديد',
            ],
            'notification' => [
                'title' => 'تم نقل العهدة',
                'body'  => 'تم نقل عهدة الأصل بنجاح.',
            ],
        ],

        'return' => [
            'label' => 'استلام / إرجاع للمخزن',
            'modal' => [
                'heading'     => 'استلام وإرجاع الأصل للمخزن',
                'description' => 'استلام الأصل والتحقق من حالته الفنية وإتاحته في المخزن.',
                'submit'      => 'تأكيد الاستلام',
            ],
            'fields' => [
                'condition-in'       => 'الحالة الفنية عند الاستلام',
                'ended-at'           => 'تاريخ ووقت الاستلام',
                'warehouse'          => 'المخزن المستلم',
                'return-note'        => 'ملاحظة الاستلام / سبب التلف',
                'return-attachments' => 'مرفقات الإرجاع',
            ],
            'notification' => [
                'title' => 'تم استلام الأصل',
                'body'  => 'تم استلام الأصل وإرجاعه إلى المخزن بنجاح.',
            ],
        ],

        'scrap' => [
            'label' => 'إتلاف',
            'modal' => [
                'heading'     => 'إتلاف أصل',
                'description' => 'تسجيل الأصل كتالف نهائيًا وإغلاق أي عهدة سارية.',
                'submit'      => 'تأكيد الإتلاف',
            ],
            'fields' => [
                'ended-at'    => 'تاريخ ووقت الإتلاف',
                'note'        => 'سبب الإتلاف',
                'attachments' => 'مرفقات التخريد',
            ],
            'notification' => [
                'title' => 'تم إتلاف الأصل',
                'body'  => 'تم تغيير حالة الأصل إلى مُتلف.',
            ],
        ],

        'mark-lost' => [
            'label' => 'تسجيل مفقود',
            'modal' => [
                'heading'     => 'تسجيل الأصل كمفقود',
                'description' => 'تسجيل فقدان الأصل وإغلاق أي عهدة سارية.',
                'submit'      => 'تأكيد الفقدان',
            ],
            'fields' => [
                'ended-at'    => 'تاريخ ووقت البلاغ',
                'note'        => 'تفاصيل الفقدان والتحقيق',
                'attachments' => 'مرفقات البلاغ',
            ],
            'notification' => [
                'title' => 'تم تسجيل الأصل كمفقود',
                'body'  => 'تم تغيير حالة الأصل إلى مفقود.',
            ],
        ],

        'bulk-register' => [
            'label' => 'تسجيل وحدات بالجملة',
            'modal' => [
                'heading'     => 'تسجيل وحدات أصول بالجملة',
                'description' => 'تسجيل عدة وحدات من نفس الموديل دفعة واحدة وبشكل متسلسل.',
                'submit'      => 'تسجيل الوحدات',
            ],
            'sections' => [
                'shared'  => 'بيانات الموديل والطلب الموحدة',
                'mode'    => 'طريقة التتبع والإدخال',
                'preview' => 'معاينة التحقق المباشر',
            ],
            'fields' => [
                'company'             => 'الشركة',
                'product'             => 'المنتج / الموديل',
                'name'                => 'اسم الأصل',
                'model'               => 'الموديل',
                'category'            => 'الفئة',
                'condition'           => 'الحالة الفنية الابتدائية',
                'cost'                => 'تكلفة الوحدة',
                'purchase-date'       => 'تاريخ الشراء',
                'warranty-until'      => 'الضمان حتى',
                'warehouse'           => 'المخزن / المستودع',
                'warehouse-help'      => 'المخزن الفعلي في نظام المخزون الذي تُحفظ فيه الأصول المتاحة المسجلة.',
                'initial-placement'   => 'التوجيه المباشر الأولي',
                'placements'          => [
                    'warehouse'        => 'إيداع في مخزن أصول',
                    'site-asset'       => 'تركيب في موقع أصول ثابتة',
                    'employee-custody' => 'تسليم كعهدة موظف',
                ],
                'site'                => 'الموقع',
                'employee'            => 'الموظف',
                'attachments'         => 'مرفقات التسليم / العهدة',
                'note'                => 'ملاحظات الدفعة',
                'tracking-mode'       => 'نمط التتبع',
                'modes'               => [
                    'with-serials'    => 'وحدات مرقمة (تتبع بالرقم التسلسلي)',
                    'without-serials' => 'وحدات غير مرقمة (أثاث، كراسي، عدد يدوية)',
                ],
                'serials-raw'         => 'قائمة الأرقام التسلسلية (سطر لكل رقم أو مفصولة بفواصل)',
                'serials-raw-help'    => 'الصق الأرقام التسلسلية: رقم تسلسلي لكل سطر أو رمز_أصل,رقم_تسلسلي.',
                'parse-to-table'      => 'تحويل إلى جدول الوحدات',
                'items'               => 'بيانات الوحدات',
                'serial-no'           => 'الرقم التسلسلي',
                'asset-tag'           => 'كود الأصل',
                'quantity'            => 'الكمية المراد توليدها',
                'tags-raw'            => 'رموز الأصول المطبوعة مسبقًا',
                'tags-raw-help'       => 'رمز أصل لكل سطر للوحدات غير المرقمة.',
                'unit-note'           => 'ملاحظة الوحدة',
            ],
            'preview' => [
                'valid'      => ':count وحدات صالحة للتسجيل',
                'duplicates' => ':count مكرر في القائمة',
                'collisions' => ':count موجود مسبقًا في النظام',
            ],
            'notification' => [
                'title' => 'تم التسجيل بالجملة',
                'body'  => 'تم تسجيل :count أصل بنجاح.',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'identity' => [
                'title'   => 'هوية الأصل',
                'entries' => [
                    'asset-tag' => 'رمز الأصل',
                    'name'      => 'اسم الأصل',
                    'product'   => 'المنتج',
                    'model'     => 'الموديل',
                    'category'  => 'الفئة',
                    'serial-no' => 'الرقم التسلسلي',
                    'status'    => 'حالة الأصل',
                    'condition' => 'الحالة الفنية',
                ],
            ],

            'custody' => [
                'title'   => 'العهدة الحالية',
                'entries' => [
                    'current-employee'   => 'الموظف الحائز',
                    'current-site'       => 'الموقع التشغيلي',
                    'site-usage'         => 'طبيعة استخدام الموقع',
                    'warehouse'          => 'المخزن / المستودع',
                    'initial-quantity'   => 'الكمية الابتدائية',
                    'available-quantity' => 'الكمية المتاحة',
                    'issued-at'          => 'تاريخ التسليم',
                    'expected-return-at' => 'تاريخ الإرجاع المتوقع',
                    'custody-note'       => 'ملاحظة العهدة',
                ],
            ],

            'acquisition' => [
                'title'   => 'الشراء والبيانات المالية',
                'entries' => [
                    'company'        => 'الشركة',
                    'purchase-date'  => 'تاريخ الشراء',
                    'warranty-until' => 'تاريخ انتهاء الضمان',
                    'cost'           => 'التكلفة',
                    'creator'        => 'أنشئ بواسطة',
                ],
            ],

            'notes' => [
                'title'   => 'الملاحظات',
                'entries' => [
                    'note' => 'الملاحظات',
                ],
            ],

            'audit' => [
                'title'   => 'سجل التدقيق',
                'entries' => [
                    'created-at' => 'تاريخ الإنشاء',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'assignments' => [
            'title'   => 'سجل الحركات والعهدة',
            'columns' => [
                'occurred-at'        => 'التاريخ والوقت',
                'type'               => 'نوع الحركة',
                'source-type'        => 'المصدر',
                'from-display'       => 'من',
                'to-display'         => 'إلى',
                'condition'          => 'الحالة الفنية',
                'performed-by'       => 'نُفذ بواسطة',
                'expected-return-at' => 'تاريخ الإرجاع المتوقع',
                'note'               => 'ملاحظة',
                'attachments'        => 'المرفقات',
            ],
            'filters' => [
                'type'        => 'نوع الحركة',
                'source-type' => 'المصدر',
            ],
            'view-modal' => [
                'heading'  => 'تفاصيل الحركة',
                'sections' => [
                    'details'     => 'بيانات الحركة',
                    'attachments' => 'المرفقات والوثائق',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'أصل جديد',
                    'notification' => [
                        'title' => 'تم إنشاء الأصل',
                        'body'  => 'تم إنشاء الأصل بنجاح.',
                    ],
                ],
            ],
            'tabs' => [
                'all'            => 'الكل',
                'available'      => 'متاح',
                'consumables'    => 'المستهلكات',
                'site-assets'    => 'أصول المواقع الثابتة',
                'with-employees' => 'عهدة موظفين',
                'at-sites'       => 'عهدة مواقع',
                'in-the-field'   => 'في الميدان',
                'overdue-return' => 'متأخر الإرجاع',
                'scrapped-lost'  => 'مُتلف / مفقود',
                'archived'       => 'المؤرشفة',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'تم إنشاء الأصل',
                'body'  => 'تم إنشاء الأصل بنجاح.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'تم تحديث الأصل',
                'body'  => 'تم تحديث الأصل بنجاح.',
            ],
        ],
    ],
];
