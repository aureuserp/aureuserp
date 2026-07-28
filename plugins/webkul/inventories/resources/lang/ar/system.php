<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'لا يوجد شيء للتحقق من توفره.',
        ],

        'cancel-move' => [
            'already-done' => 'لا يمكنك إلغاء حركة مخزون تم تعيينها على \'تم\'. أنشئ عملية إرجاع لعكس الحركات التي جرت.',
        ],

        'unreserve-move' => [
            'already-done' => "لا يمكنك إلغاء حجز حركة مخزون تم تعيينها على 'تم'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'الكمية المنجزة للمنتج ":product" لا تتوافق مع دقة التقريب المحددة لوحدة القياس ":unit". يرجى تغيير الكمية المنجزة أو دقة التقريب لوحدة القياس.',
            'no-negative-quantities'     => 'لا يُسمح بالكميات السالبة',
            'missing-lot-serial-number'  => "يجب توفير رقم الدُفعة/التسلسل للمنتج:\n:products",
        ],

        'run-procurement' => [
            'no-rule-found'      => "لم يتم العثور على قاعدة لإعادة تخزين \":product\" في \":location\".\nيرجى التحقق من تكوين المسارات على المنتج.",
            'no-source-location' => 'لا يوجد موقع مصدر محدد لقاعدة المخزون: :name!',
            'no-vendor-price'    => 'لا يوجد سعر مورد مطابق لإنشاء أمر الشراء للمنتج :product (لا يوجد مورد محدد، لم يتم الوصول إلى الحد الأدنى للكمية، التواريخ غير صالحة، ...). انتقل إلى نموذج المنتج وأكمل قائمة الموردين.',
        ],

        'return' => [
            'origin' => 'إرجاع :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'لا يُسمح بحجز كمية سالبة.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'يجب تعيين الكمية أو الكمية المحجوزة.',
        'removal-strategy-not-implemented' => 'استراتيجية الإزالة :strategy غير مطبقة.',
        'unreserve-more-than-stock'        => 'لا يمكن إلغاء حجز كمية من :name تتجاوز ما هو متاح في المخزون.',
    ],

    'product' => [
        'endless-loop-rule' => 'تكوين القاعدة غير صالح، القاعدة التالية تسبب حلقة لا نهائية: :name',
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'الكمية المنجزة للمنتج :product لا تتوافق مع دقة التقريب المحددة لوحدة القياس :unit. يرجى تغيير الكمية المنجزة أو دقة التقريب لوحدة القياس.',
        'split-done-or-cancel'       => 'لا يمكنك تقسيم حركة مخزون تم تعيينها على \'تم\' أو \'ملغاة\'.',
        'split-draft'                => 'لا يمكنك تقسيم حركة مسودة. يجب تأكيدها أولاً.',
        'serial-already-assigned'    => 'تم تعيين الرقم التسلسلي بالفعل للمنتج: :product، الرقم التسلسلي: :serial_number',
    ],

    'rule' => [
        'delay-on'     => 'تأخير على :name',
        'days'         => '+ :days يوم(أيام)',
        'time-horizon' => 'الأفق الزمني',
    ],
];
