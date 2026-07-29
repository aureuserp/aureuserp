<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => "بعض أوامر العمل مكتملة بالفعل، لذا لا يمكنك إلغاء تخطيط أمر التصنيع هذا.\n\nسيكون من المؤسف إضاعة كل هذا التقدم، أليس كذلك؟",
            'work-orders-already-started' => "بعض أوامر العمل قد بدأت بالفعل، لذا لا يمكنك إلغاء تخطيط أمر التصنيع هذا.\n\nسيكون من المؤسف إضاعة كل هذا التقدم، أليس كذلك؟",
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'تتبع الوقت: :name',
        'no-performance-productivity-loss' => "يجب تحديد خسارة إنتاجية مؤرشفة واحدة على الأقل في فئة 'الأداء'. أنشئ من إعدادات التكوين.",
    ],

    'work-center' => [
        'already-unblocked' => 'لقد تم إلغاء الحظر بالفعل.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'يرجى إلغاء حظر مركز العمل لبدء أمر العمل.',
        'already-done-or-cancelled'  => 'لا يمكنك بدء أمر عمل مكتمل أو ملغى',
        'no-calendar-on-work-center' => 'لا يوجد تقويم محدد لمركز العمل :name.',
        'no-productivity-loss'       => "يجب تحديد خسارة إنتاجية واحدة على الأقل في فئة 'الإنتاجية'. أنشئ من إعدادات التكوين.",
        'no-performance-loss'        => "يجب تحديد خسارة إنتاجية واحدة على الأقل في فئة 'الأداء'. أنشئ من إعدادات التكوين.",
        'impossible-to-plan'         => 'يستحيل تخطيط أمر العمل. يرجى التحقق من توافر مراكز العمل.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'لا يمكنك تعيين :product كمنتج نهائي وفي المنتجات الثانوية في نفس الوقت',
        'missing-lot-serial-number'                => 'يجب توفير رقم الدُفعة/التسلسل للمنتجات و"استهلاكها": :missing_products',
        'serial-number-already-produced'           => 'الرقم التسلسلي للمنتج :product تم إنتاجه بالفعل',
        'byproduct-serial-number-already-produced' => 'الرقم التسلسلي :number المستخدم للمنتج الثانوي :product تم إنتاجه بالفعل',
        'component-serial-number-consumed'         => 'الرقم التسلسلي :number المستخدم للمكوّن :component تم استهلاكه بالفعل',
        'components-availability'                  => [
            'available'     => 'متاح',
            'not-available' => 'غير متاح',
            'expected'      => 'متوقع :date',
        ],
    ],
];
