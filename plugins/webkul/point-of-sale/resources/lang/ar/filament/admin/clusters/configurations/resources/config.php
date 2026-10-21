<?php

return [
    'navigation' => [
        'title' => 'نقطة البيع',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'             => 'نقطة البيع',
                    'name-placeholder' => 'مثال: متجر نيويورك',
                    'code'             => 'الرمز المختصر',
                    'code-helper-text' => 'يُستخدم كبادئة لأرقام الطلبات والجلسات.',
                    'is-active'        => 'نشط',
                ],
            ],

            'configurations' => [
                'title' => 'الإعدادات',

                'tabs' => [
                    'restaurant' => [
                        'title' => 'وضع المطعم',

                        'fields' => [
                            'is-restaurant'                 => 'مقهى/مطعم',
                            'is-restaurant-helper-text'     => 'تفعيل إدارة الطاولات وتقسيم الفواتير وتذاكر التحضير.',
                            'enable-split-bill'             => 'السماح بتقسيم الفاتورة',
                            'enable-split-bill-helper-text' => 'تقسيم الإجمالي أو بنود الطلب.',
                            'enable-print-bill'             => 'طباعة الإيصال المبكرة',
                            'enable-print-bill-helper-text' => 'السماح بطباعة الإيصال قبل الدفع.',
                            'enable-takeaway'               => 'تناول في المكان / سفري',
                            'enable-takeaway-helper-text'   => 'تعديل نسبة الضريبة حسب تناول العملاء في المكان أو أخذ الطلب.',
                            'takeaway-fiscal-position'      => 'وضع ضريبي بديل',
                            'floors'                        => 'خريطة الطوابق والطاولات',
                            'floors-helper-text'            => 'صمّم الطوابق وخصّص الطلبات للطاولات.',
                        ],
                    ],

                    'payment' => [
                        'title' => 'دفعة',

                        'fields' => [
                            'payment-methods'                        => 'طرق الدفع',
                            'enable-maximum-difference'              => 'تحديد أقصى فرق',
                            'enable-maximum-difference-helper-text'  => 'حدّد أقصى فرق مسموح به بين المبلغ المتوقع والمعدود عند إغلاق الجلسة.',
                            'amount-authorized-diff'                 => 'أقصى فرق',
                            'enable-cash-rounding'                   => 'التقريب النقدي',
                            'enable-cash-rounding-helper-text'       => 'حدّد أصغر فئة نقدية للعملة المستخدمة في الدفع النقدي.',
                            'cash-rounding'                          => 'طريقة التقريب',
                            'enable-only-round-cash-method'          => 'تطبيق التقريب على النقد فقط',
                            'enable-tip'                             => 'الإكراميات',
                            'enable-tip-helper-text'                 => 'قبول إكراميات العملاء أو تحويل الباقي إلى إكرامية.',
                            'tip-product'                            => 'منتج الإكرامية',
                        ],
                    ],

                    'interface' => [
                        'title' => 'واجهة نقطة البيع',

                        'fields' => [
                            'enable-customer-required'              => 'العميل مطلوب',
                            'show-product-images'                   => 'إظهار صور المنتجات',
                            'show-category-images'                  => 'إظهار صور الفئات',
                            'limited-products-amount'               => 'المنتجات المحمّلة',
                            'limited-products-amount-helper-text'   => 'عدد المنتجات التي تُحمّل في الطرفية عند فتح الجلسة.',
                        ],
                    ],

                    'products' => [
                        'title' => 'المنتجات وفئات نقطة البيع',

                        'fields' => [
                            'show-margins-and-costs'             => 'إظهار الهوامش والتكاليف',
                            'show-margins-and-costs-helper-text' => 'إظهار الهوامش والتكاليف في معلومات المنتج.',
                            'limit-categories'                   => 'تقييد الفئات',
                            'limit-categories-helper-text'       => 'اختر فئات منتجات نقطة البيع المتاحة.',
                            'locked-by-session'                  => 'مقفل أثناء وجود جلسة مفتوحة',
                            'categories'                         => 'فئات منتجات نقطة البيع المتاحة',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'المحاسبة',

                        'fields' => [
                            'journal'                                  => 'دفتر يومية الطلبات',
                            'journal-helper-text'                      => 'دفتر اليومية المستخدم لقيد إغلاق الجلسة.',
                            'invoice-journal'                          => 'دفتر يومية الفواتير',
                            'is-closing-entry-by-product'              => 'قيد الإغلاق حسب المنتج',
                            'is-closing-entry-by-product-helper-text'  => 'عرض تفصيل بنود المبيعات حسب المنتج في قيد الإغلاق المُنشأ تلقائياً.',
                            'enable-fiscal-position'                   => 'ضرائب مرنة',
                            'enable-fiscal-position-helper-text'       => 'استخدام الأوضاع الضريبية للحصول على ضرائب مختلفة لكل طلب.',
                            'fiscal-position'                          => 'الوضع الضريبي الافتراضي',
                            'fiscal-positions'                         => 'الأوضاع الضريبية',
                            'receivable-account'                       => 'الحساب الوسيط',
                            'receivable-account-helper-text'           => 'اتركه فارغاً لاستخدام حساب الذمم المدينة الافتراضي من إعدادات نقطة البيع.',
                            'cash-movement-account'                    => 'حساب الإيداع/السحب النقدي',
                            'balancing-account'                        => 'حساب الموازنة',
                            'enable-cogs'                              => 'تكلفة البضاعة المباعة',
                            'enable-cogs-helper-text'                  => 'ترحيل تكلفة المنتجات المباعة عند إغلاق الجلسة.',
                            'cogs-journal'                             => 'دفتر يومية تكلفة البضاعة المباعة',
                            'stock-output-account'                     => 'حساب إخراج المخزون',
                        ],
                    ],

                    'pricing' => [
                        'title' => 'التسعير',

                        'fields' => [
                            'tax-display'                            => 'أسعار المنتجات',
                            'tax-display-helper-text'                => 'أسعار المنتجات المعروضة على الطرفية وعلى الإيصالات.',
                            'enable-price-control'                   => 'التحكم في السعر',
                            'enable-price-control-helper-text'       => 'قصر تعديل السعر على المديرين.',
                            'enable-price-list'                      => 'قوائم أسعار مرنة',
                            'enable-price-list-helper-text'          => 'حدّد أسعاراً متعددة لكل منتج وخصومات آلية وغير ذلك.',
                            'price-list'                             => 'قائمة الأسعار الافتراضية',
                            'price-list-helper-text'                 => 'تُطبَّق على كل طلب في هذا السجل النقدي. لا يمكن لأمناء الصندوق تبديل القوائم إلا عند تفعيل قوائم الأسعار المرنة.',
                            'price-lists'                            => 'قوائم الأسعار المتاحة',
                            'enable-line-discount'                   => 'خصومات البنود',
                            'enable-line-discount-helper-text'       => 'السماح لأمناء الصندوق بتحديد خصم لكل بند.',
                            'enable-global-discount'                 => 'الخصومات الإجمالية',
                            'enable-global-discount-helper-text'     => 'يضيف زراً لتحديد خصم إجمالي.',
                            'discount-product'                       => 'منتج الخصم',
                            'global-discount-percentage'             => 'نسبة الخصم %',
                            'global-discount-percentage-helper-text' => 'النسبة الافتراضية المعروضة عند تطبيق الكاشير خصماً على الطلب.',
                        ],
                    ],

                    'receipts' => [
                        'title' => 'الفواتير والإيصالات',

                        'fields' => [
                            'enable-receipt-print'                   => 'طباعة الإيصال',
                            'enable-receipt-print-helper-text'       => 'إظهار زر الطباعة في شاشة الإيصال بعد الدفع.',
                            'enable-receipt-auto-print'              => 'طباعة الإيصال تلقائياً',
                            'enable-receipt-auto-print-helper-text'  => 'طباعة الإيصالات تلقائياً بمجرد تسجيل الدفعة.',
                            'receipt-header'                         => 'ترويسة الإيصال',
                            'receipt-footer'                         => 'تذييل الإيصال',
                            'bills'                                  => 'العملات/الأوراق النقدية',
                            'bills-helper-text'                      => 'الفئات النقدية المعروضة عند عدّ النقد عند الفتح والإغلاق.',
                        ],
                    ],

                    'preparation' => [
                        'title' => 'التحضير',

                        'fields' => [
                            'printers'             => 'طابعات التحضير',
                            'printers-helper-text' => 'طباعة الطلبات في المطبخ أو البار أو غير ذلك.',
                        ],
                    ],

                    'inventory' => [
                        'title' => 'المخزون',

                        'fields' => [
                            'operation-type'                => 'نوع العملية',
                            'operation-type-helper-text'    => 'يُستخدم لتسجيل عمليات سحب المنتجات. تُستهلك المنتجات من موقع المصدر الافتراضي.',
                            'return-operation-type'         => 'نوع عملية الإرجاع',
                            'warehouse'                     => 'المستودع',
                            'enable-ship-later'             => 'السماح بالشحن لاحقاً',
                            'enable-ship-later-helper-text' => 'بيع المنتجات وتسليمها لاحقاً.',
                            'ship-later-route'              => 'مسار محدد',
                            'picking-policy'                => 'سياسة الشحن',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'name'           => 'نقطة البيع',
                    'code'           => 'الرمز المختصر',
                    'company-name'   => 'الشركة',
                    'warehouse-name' => 'المستودع',
                    'journal-name'   => 'دفتر يومية الطلبات',
                    'is-restaurant'  => 'مقهى/مطعم',
                    'is-active'      => 'نشط',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'الاسم',
            'closing'        => 'الإغلاق',
            'balance'        => 'الرصيد',
            'code'           => 'الرمز',
            'warehouse'      => 'المستودع',
            'operation-type' => 'نوع العملية',
            'journal'        => 'دفتر يومية المبيعات',
            'is-restaurant'  => 'مطعم',
            'is-active'      => 'نشط',
            'company'        => 'الشركة',
        ],

        'groups' => [
            'warehouse' => 'المستودع',
            'company'   => 'الشركة',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'تمت استعادة نقطة البيع',
                        'body'  => 'تمت استعادة نقطة البيع.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف نقطة البيع',
                        'body'  => 'تم حذف نقطة البيع.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف نقطة البيع نهائياً',
                        'body'  => 'تم حذف نقطة البيع نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف نقطة البيع',
                        'body'  => 'لا يمكن حذف نقطة البيع نهائياً لأنها تحتوي على جلسات أو طلبات. قم بأرشفتها بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف نقاط البيع نهائياً',
                        'body'  => 'تم حذف نقاط البيع المحددة نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف نقاط البيع',
                        'body'  => 'لا يمكن حذف نقطة بيع واحدة أو أكثر نهائياً لأنها تحتوي على جلسات أو طلبات. قم بأرشفتها بدلاً من ذلك.',
                    ],
                ],
            ],
        ],
    ],
];
