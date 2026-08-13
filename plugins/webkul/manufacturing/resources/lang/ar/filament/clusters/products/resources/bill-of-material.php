<?php

return [
    'navigation' => [
        'title' => 'قوائم المواد',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'reference'             => 'المرجع',
                    'reference-placeholder' => 'مثال: BOM-001',
                    'product'               => 'المنتج',
                    'product-variant'       => 'متغير المنتج',
                    'quantity'              => 'الكمية',
                    'uom'                   => 'وحدة القياس',
                    'operation-type'        => 'نوع العملية',
                    'company'               => 'الشركة',
                    'type'                  => 'نوع قائمة المواد',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'متفرقات',
                'fields' => [
                    'kit-information'                     => 'معلومات الطقم',
                    'kit-information-content'             => 'تُستخدم قائمة مواد الطقم لتجميع المكونات لعمليات النقل أو المبيعات، بدلاً من إنتاجها عبر أمر تصنيع.',
                    'manufacturing-lead-time'             => 'وقت تسليم التصنيع',
                    'days-to-prepare-manufacturing-order' => 'أيام التحضير لأمر التصنيع',
                    'days-suffix'                         => 'أيام',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'المكونات',
                'add-action' => 'إضافة سطر',
                'columns'    => [
                    'component'             => 'المكون',
                    'apply-on-variants'     => 'تطبيق على المتغيرات',
                    'consumed-in-operation' => 'يُستهلك في العملية',
                    'highlight-consumption' => 'تسليط الضوء على الاستهلاك',
                    'quantity'              => 'الكمية',
                    'uom'                   => 'وحدة قياس المنتج',
                ],
                'validation' => [
                    'component-different-from-product' => 'يجب أن يكون المكون مختلفًا عن المنتج المراد تصنيعه.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'الاسم',
                        'type'            => 'النوع',
                        'category'        => 'الفئة',
                        'company'         => 'الشركة',
                        'uom'             => 'وحدة القياس',
                        'uom-placeholder' => 'وحدة القياس',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'العمليات',
                'add-action' => 'إضافة سطر',
                'actions'    => [
                    'edit'                 => 'تعديل العملية',
                    'copy-existing'        => 'نسخ العمليات الموجودة',
                    'copy-existing-fields' => [
                        'operation' => 'العملية',
                    ],
                ],
                'columns' => [
                    'operation'         => 'العملية',
                    'work-center'       => 'مركز العمل',
                    'time-mode'         => 'احتساب المدة',
                    'time-mode-batch'   => 'محسوب على آخر',
                    'company'           => 'الشركة',
                    'apply-on-variants' => 'تطبيق على المتغيرات',
                    'duration'          => 'المدة (دقائق)',
                ],
            ],
            'by-products' => [
                'title'      => 'المنتجات الثانوية',
                'add-action' => 'إضافة سطر',
                'columns'    => [
                    'product'   => 'المنتج الثانوي',
                    'quantity'  => 'الكمية',
                    'uom'       => 'وحدة القياس',
                    'operation' => 'يُنتج في العملية',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'متفرقات',
                'fields' => [
                    'ready-to-produce'       => 'الجاهزية للتصنيع',
                    'routing'                => 'المسار',
                    'consumption'            => 'الاستهلاك المرن',
                    'operation-dependencies' => 'تبعيات العمليات',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'المرجع',
            'product'    => 'المنتج',
            'quantity'   => 'الكمية',
            'uom'        => 'وحدة القياس',
            'type'       => 'نوع قائمة المواد',
            'company'    => 'الشركة',
            'deleted-at' => 'تاريخ الحذف',
            'updated-at' => 'تاريخ التحديث',
        ],
        'filters' => [
            'product' => 'المنتج',
            'type'    => 'نوع قائمة المواد',
            'company' => 'الشركة',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة قائمة المواد',
                    'body'  => 'تمت استعادة قائمة المواد بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة قائمة المواد',
                    'body'  => 'تمت أرشفة قائمة المواد بنجاح.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف قائمة المواد',
                        'body'  => 'تم حذف قائمة المواد نهائياً.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف قائمة المواد',
                        'body'  => 'لا يمكن حذف قائمة المواد لأنها مستخدمة حالياً.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة قوائم المواد',
                    'body'  => 'تمت استعادة قوائم المواد المحددة بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة قوائم المواد',
                    'body'  => 'تمت أرشفة قوائم المواد المحددة بنجاح.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف قوائم المواد',
                        'body'  => 'تم حذف قوائم المواد المحددة نهائياً.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف قوائم المواد',
                        'body'  => 'واحدة أو أكثر من قوائم المواد المحددة مستخدمة حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'المعلومات العامة',
                'entries' => [
                    'reference'       => 'المرجع',
                    'product'         => 'المنتج',
                    'product-variant' => 'متغير المنتج',
                    'quantity'        => 'الكمية',
                    'uom'             => 'وحدة القياس',
                    'operation-type'  => 'نوع العملية',
                    'company'         => 'الشركة',
                    'type'            => 'نوع قائمة المواد',
                ],
            ],
            'record-information' => [
                'title'   => 'معلومات السجل',
                'entries' => [
                    'created-by'   => 'أنشئ بواسطة',
                    'created-at'   => 'تاريخ الإنشاء',
                    'last-updated' => 'آخر تحديث',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'المكونات',
                'entries' => [
                    'component' => 'المكون',
                    'operation' => 'العملية',
                    'quantity'  => 'الكمية',
                    'uom'       => 'وحدة قياس المنتج',
                ],
            ],
            'operations' => [
                'title'   => 'العمليات',
                'entries' => [
                    'operation'   => 'العملية',
                    'work-center' => 'مركز العمل',
                    'time-mode'   => 'احتساب المدة',
                    'duration'    => 'المدة (دقائق)',
                ],
            ],
            'by-products' => [
                'title'   => 'المنتجات الثانوية',
                'entries' => [
                    'product'   => 'المنتج الثانوي',
                    'quantity'  => 'الكمية',
                    'uom'       => 'وحدة القياس',
                    'operation' => 'يُنتج في العملية',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'متفرقات',
                'entries' => [
                    'kit-information'                     => 'معلومات الطقم',
                    'kit-information-content'             => 'تُستخدم قائمة مواد الطقم لتجميع المكونات لعمليات النقل أو المبيعات، بدلاً من إنتاجها عبر أمر تصنيع.',
                    'ready-to-produce'                    => 'الجاهزية للتصنيع',
                    'routing'                             => 'المسار',
                    'consumption'                         => 'الاستهلاك المرن',
                    'operation-dependencies'              => 'تبعيات العمليات',
                    'manufacturing-lead-time'             => 'وقت تسليم التصنيع',
                    'days-to-prepare-manufacturing-order' => 'أيام التحضير لأمر التصنيع',
                    'days-suffix'                         => 'أيام',
                ],
            ],
        ],
    ],
];
