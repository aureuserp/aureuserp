<?php

return [
    'actions' => [
        'export' => [
            'label' => 'تصدير',
        ],

        'import' => [
            'label' => 'استيراد',
        ],
    ],

    'columns' => [
        'id'                 => 'المعرف',
        'name'               => 'الاسم',
        'contact-name'       => 'اسم جهة الاتصال',
        'company-name'       => 'اسم الشركة',
        'job-title'          => 'المسمى الوظيفي',
        'email'              => 'البريد الإلكتروني',
        'phone'              => 'الهاتف',
        'website'            => 'الموقع الإلكتروني',
        'street1'            => 'الشارع',
        'street2'            => 'الشارع 2',
        'city'               => 'المدينة',
        'zip'                => 'الرمز البريدي',
        'country'            => 'الدولة',
        'stage'              => 'المرحلة',
        'salesperson'        => 'مندوب المبيعات',
        'team'               => 'فريق المبيعات',
        'priority'           => 'الأولوية',
        'probability'        => 'الاحتمالية',
        'tags'               => 'الوسوم',
        'source'             => 'المصدر',
        'medium'             => 'الوسيط',
        'campaign'           => 'الحملة',
        'notes'              => 'ملاحظات',
        'lost-reason'        => 'سبب الخسارة',
        'created-at'         => 'تاريخ الإنشاء',
        'customer'           => 'العميل',
        'expected-revenue'   => 'الإيرادات المتوقعة',
        'prorated-revenue'   => 'الإيرادات المرجحة',
        'expected-closing'   => 'الإغلاق المتوقع',
        'number'             => 'الرقم',
        'order-date'         => 'تاريخ الطلب',
        'expiration'         => 'تاريخ الانتهاء',
        'delivery-date'      => 'تاريخ التسليم',
        'customer-reference' => 'مرجع العميل',
        'source-document'    => 'المستند المصدر',
        'untaxed-amount'     => 'المبلغ قبل الضريبة',
        'taxes'              => 'الضرائب',
        'total'              => 'الإجمالي',
        'currency'           => 'العملة',
        'status'             => 'الحالة',
        'invoice-status'     => 'حالة الفوترة',
    ],

    'notifications' => [
        'export' => [
            'completed' => 'التصدير جاهز: تم تصدير :count صف.',
            'failed'    => 'فشل تصدير :count صف.',
        ],

        'import' => [
            'completed' => 'اكتمل الاستيراد: تم استيراد :count صف.',
            'failed'    => 'فشل :count صف؛ قم بتنزيلها من هذا الإشعار لمعرفة السبب.',
        ],
    ],

    'errors' => [
        'record-not-found' => 'لم يتم العثور على سجل بالمعرف :id في شركاتك.',
        'cannot-update'    => 'غير مسموح لك بتحديث السجل :id.',
        'cannot-create'    => 'غير مسموح لك بإنشاء سجلات.',
        'not-found'        => 'لم يتم العثور على :field ":value".',
    ],
];
