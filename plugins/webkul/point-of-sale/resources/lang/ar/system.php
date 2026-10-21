<?php

return [
    'unexpected-error' => 'حدث خطأ في الخادم. حاول مرة أخرى، واطلب من المدير المساعدة إذا تكرر ذلك.',

    'products' => [
        'tip'      => 'الإكراميات',
        'discount' => 'الخصم',
    ],

    'picking' => [
        'operation-type-missing' => 'لا يوجد نوع عملية مخزون مهيأ للطلب :order، لذلك لم تُنقل أي بضاعة.',
    ],

    'config' => [
        'terminal-journal' => 'نقطة البيع',
    ],

    'order-workflow' => [
        'customer' => [
            'required-by-terminal'       => 'تتطلب نقطة البيع هذه عميلاً في كل طلب.',
            'required-to-invoice'        => 'اختر عميلاً قبل فوترة هذا الطلب.',
            'required-to-ship'           => 'اختر عميلاً قبل شحن هذا الطلب لاحقاً.',
            'shipping-address-required'  => 'يحتاج العميل المحدد إلى عنوان تسليم قبل شحن هذا الطلب لاحقاً.',
            'required-by-payment-method' => 'طريقة الدفع المحددة تتطلب عميلاً.',
        ],

        'mark-paid' => [
            'already-settled'          => 'تمت تسوية الطلب :order بالفعل.',
            'insufficient-payment'     => 'الطلب :order غير مدفوع بالكامل.',
            'cash-method-required'     => 'لا يمكن دفع الطلب :order بأكثر من المستحق لأن نقطة البيع هذه ليس لها طريقة دفع نقدي مُهيّأة.',
        ],

        'cancel' => [
            'not-draft' => 'لم يعد بالإمكان إلغاء الطلب :order.',
        ],

        'split' => [
            'not-draft' => 'لم يعد بالإمكان تقسيم الطلب :order.',
        ],

        'tip' => [
            'not-enabled' => 'فعّل الإكراميات وحدّد منتج إكرامية في نقطة البيع أولاً.',
        ],

        'refund' => [
            'not-refundable'    => 'لا يمكن استرداد الطلب :order.',
            'exceeds-sold'      => 'الكمية المستردة تتجاوز ما تم بيعه من :product.',
            'no-payment-method' => 'لا توجد لدى الطرفية :order طريقة دفع للاسترداد بها.',
            'nothing-to-refund' => 'اختر بنداً واحداً على الأقل للاسترداد.',
        ],
    ],

    'global-discount' => [
        'not-enabled' => 'فعّل الخصم الإجمالي وحدّد منتج خصم في نقطة البيع أولاً.',
        'not-draft'   => 'لم يعد بالإمكان خصم الطلب :order.',
    ],

    'terminal-product-creator' => [
        'name-required'    => 'أعطِ المنتج اسماً.',
        'defaults-missing' => 'جهّز وحدة قياس وفئة منتجات قبل إنشاء منتجات في نقطة البيع.',
    ],

    'payment-method' => [
        'in-open-session' => 'أغلق الجلسات المفتوحة :sessions قبل حذف طريقة الدفع هذه.',
    ],

    'payment-method-provisioner' => [
        'cash'         => 'نقدي',
        'cash-journal' => 'النقد',
    ],

    'order-sync' => [
        'product-missing'         => 'لم يعد أحد منتجات هذا الطلب موجودًا. احذفه وحاول مرة أخرى.',
        'customer-missing'        => 'لم يعد عميل هذا الطلب موجودًا. اختر عميلًا آخر.',
        'table-missing'           => 'لم تعد طاولة هذا الطلب موجودة. انقل الطلب إلى طاولة أخرى.',
        'price-list-missing'      => 'لم تعد قائمة أسعار هذا الطلب موجودة.',
        'fiscal-position-missing' => 'لم يعد الوضع الضريبي لهذا الطلب موجودًا.',
        'payment-method-missing'  => 'لم تعد إحدى طرق الدفع في هذا الطلب موجودة.',
        'refunded-order-missing'  => 'لم يعد بالإمكان العثور على الطلب المسترد.',
        'customer-email-invalid'  => 'عنوان البريد الإلكتروني للعميل غير صالح.',
        'discount-invalid'        => 'يجب أن تكون خصومات البنود بين 0 و100%.',
    ],

    'order-processor' => [
        'price-locked'           => 'تغيير سعر :product يتطلب موافقة المدير.',
        'line-discount-disabled' => 'خصومات البنود معطلة في نقطة البيع هذه.',
        'foreign-order'          => 'هذا الطلب يخص صندوقًا آخر ولا يمكن تعديله هنا.',
        'foreign-line'           => 'أحد عناصر هذا الطلب يخص طلبًا آخر. أعد تحميل الصندوق وحاول مرة أخرى.',
    ],

    'floor-plan' => [
        'tables-in-use' => 'لا تزال الطاولات :tables تحتوي على طلبات مفتوحة. ادفعها أو حررها أو انقلها أولاً.',
    ],

    'session-preflight' => [
        'draft-orders' => 'ادفع هذه الطلبات أو ألغِها قبل إغلاق الجلسة: :orders.',

        'journal' => [
            'missing'      => 'حدّد دفتر يومية مبيعات في نقطة البيع.',
            'invalid-type' => 'يجب أن يكون دفتر يومية نقطة البيع دفتر مبيعات.',
        ],

        'invoice-journal' => [
            'missing'      => 'حدّد دفتر يومية فواتير في نقطة البيع.',
            'invalid-type' => 'يجب أن يكون دفتر يومية الفواتير دفتر مبيعات.',
        ],

        'receivable-account' => [
            'missing'          => 'حدّد حساب ذمم مدينة في نقطة البيع.',
            'invalid-type'     => 'يجب أن يكون حساب الذمم المدينة لنقطة البيع حساب ذمم مدينة.',
            'not-reconcilable' => 'يجب أن يسمح حساب الذمم المدينة لنقطة البيع بالتسوية.',
            'deprecated'       => 'حساب الذمم المدينة لنقطة البيع مهمل.',
        ],

        'payment-methods' => [
            'missing'               => 'أضف طريقة دفع واحدة على الأقل إلى نقطة البيع.',
            'pay-later-unsupported' => 'طريقة الدفع :method ليس لها دفتر يومية وحسابات العملاء غير مدعومة بعد.',
            'journal-missing'       => 'حدّد دفتر يومية لطريقة الدفع :method.',
            'journal-company'       => 'دفتر يومية :method يخص شركة أخرى.',
            'receivable-missing'    => 'حدّد حساب ذمم مدينة لـ :method.',
        ],

        'cash-journal' => [
            'multiple'             => 'يُدعم استخدام طريقة دفع نقدية واحدة فقط لكل نقطة بيع.',
            'profit-loss-missing'  => 'حدّد حسابي الأرباح والخسائر في دفتر اليومية النقدي :journal.',
        ],

        'taxes' => [
            'account-missing' => 'الضريبة :tax لها بند توزيع بدون حساب.',
        ],

        'global-discount' => [
            'product-missing' => 'حدّد منتج الخصم في نقطة البيع لاستخدام الخصم العام.',
        ],

        'cogs' => [
            'stock-output-missing' => 'حدّد حساب إخراج المخزون قبل تفعيل تكلفة البضاعة المباعة.',
        ],
    ],

    'invoice-payer' => [
        'entry-reference' => 'دفعة فاتورة للطلب :order (:invoice) باستخدام :method',
    ],

    'session-closer' => [
        'entry-reference'                 => 'جلسة نقطة البيع :session',
        'payment-difference-reference'    => 'فرق على :method لـ :session',
        'cogs-reference'                  => 'تكلفة البضاعة المباعة لـ :session',
        'sales-line'                      => 'مبيعات نقطة البيع',
        'tax-line'                        => 'ضرائب نقطة البيع',
        'receivable-line'                 => 'ذمم نقطة البيع المدينة',
        'invoice-receivable-line'         => 'ذمم فواتير نقطة البيع المدينة',
        'cash-line'                       => 'نقدية نقطة البيع',
        'cash-difference-line'            => 'الفرق النقدي عند الإغلاق',
        'cogs-line'                       => 'تكلفة البضاعة المباعة',
        'stock-output-line'               => 'إخراج المخزون',
        'balancing-line'                  => 'الفرق عند الإغلاق',
        'rounding-line'                   => 'التقريب النقدي',
        'unbalanced'                      => 'قيد الإغلاق غير متوازن بمقدار :delta. اختر حساب موازنة لترحيله.',
        'income-account-missing'          => 'لم يُحدَّد حساب إيرادات لـ :product.',
        'expense-account-missing'         => 'لم يُحدَّد حساب مصروفات لـ :product.',
        'cash-account-missing'            => 'دفتر اليومية النقدي ليس له حساب افتراضي.',
        'cash-difference-account-missing' => 'دفتر اليومية النقدي ليس له حساب أرباح أو خسائر.',
        'stock-output-missing'            => 'حدّد حساب إخراج المخزون في نقطة البيع.',
        'line-account-missing'            => 'أحد بنود قيد الإغلاق بدون حساب.',
        'rounding-account-missing'        => 'التقريب النقدي ليس له حساب أرباح أو خسائر.',
        'difference-exceeded'             => 'الحد الأقصى المسموح للفرق هو :amount. يرجى التواصل مع المدير لقبول فرق الإغلاق.',
    ],

    'invoicer' => [
        'customer-required' => 'يحتاج الطلب :order إلى عميل قبل أن تتم فوترته.',
        'journal-missing'   => 'حدّد دفتر يومية فواتير في نقطة البيع.',
        'session-closed'    => 'ينتمي الطلب :order إلى جلسة مغلقة وقد تمت محاسبته بالفعل في قيد الإغلاق.',
    ],

    'session-workflow' => [
        'open' => [
            'already-open' => 'توجد جلسة مفتوحة بالفعل لـ :config.',
            'inactive'     => 'السجل :config غير نشط ولا يمكن فتحه.',
        ],

        'assert-open' => [
            'not-open' => 'الجلسة :session ليست مفتوحة.',
        ],

        'assert-state' => [
            'invalid' => 'لا يمكن نقل الجلسة :session من :state.',
        ],

        'discard' => [
            'not-discardable' => 'الجلسة :name لها نشاط مسجّل عليها ويجب إغلاقها لا تجاهلها.',
        ],

        'cash-movement' => [
            'invalid-amount' => 'يجب أن يكون مبلغ الحركة النقدية أكبر من صفر.',
        ],
    ],
];
