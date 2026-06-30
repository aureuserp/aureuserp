<?php

return [
    'title' => 'الباركود',

    'navigation' => [
        'back'        => 'رجوع',
        'home'        => 'العمليات',
        'search'      => 'بحث...',
        'label'       => 'التنقل',
        'open'        => 'فتح التنقل',
        'coming-soon' => 'قريباً',
    ],

    'auth' => [
        'login-title'       => 'تسجيل الدخول إلى الباركود',
        'login-heading'     => 'تسجيل الدخول إلى الباركود',
        'login-subheading'  => 'المتابعة إلى تطبيق عمليات الباركود.',
    ],

    'filament' => [
        'navigation' => [
            'group' => 'الباركود',
            'label' => 'تطبيق الباركود',
        ],
    ],

    'dashboard' => [
        'operations' => 'العمليات',
        'empty'      => 'لا توجد عمليات متاحة.',
    ],

    'operation-search' => [
        'placeholder'    => 'امسح أو أدخل باركود العملية...',
        'open'           => 'فتح',
        'not-found'      => 'لم يتم العثور على عملية نشطة لهذا الباركود.',
        'multiple-found' => 'تم العثور على :count عملية مطابقة.',
    ],

    'transfers' => [
        'title' => 'التحويلات',
        'empty' => 'لم يتم العثور على تحويلات.',
    ],

    'adjustments' => [
        'title'             => 'تسويات المخزون',
        'subtitle'          => 'احسب المخزون حسب الموقع أو المنتج أو الدفعة',
        'search'            => 'امسح أو ابحث حسب الموقع، المنتج، الدفعة، الرقم التسلسلي...',
        'empty'             => 'لم يتم العثور على كميات في المخزون.',
        'location-scanned'  => 'جارٍ مسح الموقع :location. امسح المزيد من المنتجات هنا أو امسح موقعاً آخر.',
        'location-cleared'  => 'تم مسح عوامل تصفية تسوية المخزون.',
        'product-not-found' => 'هذا المنتج غير متاح في تحديد المخزون الحالي.',
        'lot-not-found'     => 'هذه الدفعة أو الرقم التسلسلي غير متاح في تحديد المخزون الحالي.',
        'multiple-found'    => 'تم العثور على :count كمية مخزون مطابقة.',
        'count-saved'       => 'تم حفظ جرد المخزون.',
        'count-applied'     => 'تم تطبيق تسوية المخزون.',
        'count-cleared'     => 'تم مسح جرد المخزون.',
        'counted'           => 'تم العد',
        'on-hand'           => 'المتوفر',
        'location'          => 'الموقع',
        'product'           => 'المنتج',
        'lot-serial'        => 'الدفعة/الرقم التسلسلي',
        'clear-filters'     => 'مسح عوامل التصفية',
        'apply'             => 'تطبيق',
        'clear'             => 'مسح',
        'editor-title'      => 'تفاصيل التسوية',
        'editor-subtitle'   => 'راجع تفاصيل المخزون وحدّث الكمية المعدودة.',
        'editor-image'      => 'صورة كمية المخزون',
        'edit-tooltip'      => 'تعديل كمية المخزون',
    ],

    'operation' => [
        'scan'                 => 'امسح منتجاً أو دفعة أو طرداً أو تغليفاً أو تحويلاً',
        'manual-scan'          => 'امسح أو ابحث حسب المنتج، المرجع، الباركود...',
        'search'               => 'ابحث عن منتج، مرجع، باركود...',
        'moves'                => 'الحركات',
        'source'               => 'من',
        'available'            => 'متاح',
        'discard'              => 'تجاهل',
        'confirm'              => 'تأكيد',
        'counted'              => 'تم العد',
        'lot-serial'           => 'رقم الدفعة/الرقم التسلسلي',
        'stock-title'          => 'الكمية في المخزون',
        'empty-moves'          => 'لم يتم العثور على حركات.',
        'details-title'        => 'تفاصيل الحركة',
        'settings-title'       => 'إعدادات الحركة',
        'pick-from'            => 'الانتقاء من',
        'destination-location' => 'موقع الوجهة',
        'destination-package'  => 'طرد الوجهة',
        'select-package'       => 'اختر الطرد',
        'stock-subtitle'       => 'اختر من أين أيضاً يتم انتقاء المنتج',
        'no-stock-locations'   => 'لم يتم العثور على مواقع مخزون.',
        'camera-unavailable'   => 'الكاميرا غير متاحة',
        'submit-scan'          => 'إرسال المسح',
        'image-alt'            => 'صورة سطر الحركة',
        'edit-tooltip'         => 'تعديل سطر الحركة',
    ],

    'scan' => [
        'empty'                    => 'أدخل أو امسح باركود.',
        'not-found'                => 'لم يتم العثور على باركود مطابق.',
        'operation-matched'        => 'تم مطابقة التحويل.',
        'product-not-on-operation' => 'هذا المنتج ليس جزءاً من العملية.',
        'package-matched'          => 'تم مطابقة الطرد.',
        'move-located'             => 'تم تحديد الحركة. أدخل الكمية المعدودة.',
        'move-updated'             => 'تم تحديث كمية الحركة.',
        'move-counted'             => 'تم تعليم الحركة كمعدودة.',
    ],

    'actions' => [
        'confirm'                  => 'تأكيد',
        'confirm-prompt'           => 'هل أنت متأكد أنك تريد',
        'cancel'                   => 'إلغاء',
        'check-availability'       => 'التحقق من التوفر',
        'validate'                 => 'تحقق',
        'return'                   => 'إرجاع',
        'stay-on-transfer'         => 'تجاهل',
        'no-backorder'             => 'بدون طلب مؤجل',
        'backorder-title'          => 'تحويل غير مكتمل',
        'backorder-prompt'         => 'إذا قمت بالتحقق الآن، ستتم إضافة المنتجات المتبقية إلى طلب مؤجل.',
        'backorder-col-product'    => 'المنتج',
        'backorder-col-done-todo'  => 'منجز / للتنفيذ',
        'backorder-col-backorder'  => 'الطلب المؤجل',
        'completed'                => 'تم إكمال الإجراء.',
        'unsupported'              => 'إجراء باركود غير مدعوم.',
        'no-moves'                 => 'لا توجد حركات لهذه العملية.',
        'no-return-quantities'     => 'لا توجد كميات للإرجاع.',
    ],
];
