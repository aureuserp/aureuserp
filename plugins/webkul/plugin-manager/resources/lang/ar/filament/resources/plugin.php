<?php

return [

    'title' => 'إضافة',

    'table' => [
        'version'             => 'الإصدار',
        'dependencies'        => 'التبعيات',
        'dependencies_suffix' => 'تبعيات',
    ],

    'status' => [
        'installed'     => 'مُثبَّت',
        'not_installed' => 'غير مُثبَّت',
    ],

    'filters' => [
        'installation_status' => 'حالة التثبيت',
        'all_plugins'         => 'جميع الإضافات',
        'installed'           => 'مُثبَّت',
        'not_installed'       => 'غير مُثبَّت',
        'active_status'       => 'حالة التفعيل',
        'author'              => 'المؤلف',
        'webkul'              => 'Webkul',
        'third_party'         => 'طرف ثالث',
    ],

    'actions' => [
        'install' => [
            'title'       => 'تثبيت',
            'heading'     => 'تثبيت الإضافة :name',
            'description' => "هل أنت متأكد أنك تريد تثبيت إضافة ':name'؟ سيتم تشغيل التهجيرات والبذور.",
            'submit'      => 'تثبيت الإضافة',
        ],
        'uninstall' => [
            'title'      => 'إلغاء التثبيت',
            'heading'    => 'إلغاء تثبيت الإضافة',
            'submit'     => 'إلغاء تثبيت الإضافة',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'تم تثبيت الإضافة بنجاح',
            'body'  => "تم تثبيت إضافة ':name'.",
        ],
        'installed-failed' => [
            'title' => 'فشل التثبيت',
        ],
        'uninstalled' => [
            'title' => 'تم إلغاء تثبيت الإضافة بنجاح',
            'body'  => "تم إلغاء تثبيت إضافة ':name'.",
        ],
        'uninstalled-failed' => [
            'title' => 'فشل إلغاء التثبيت',
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => 'معلومات الإضافة',
            'dependencies' => 'التبعيات',
        ],
        'name'         => 'اسم الإضافة',
        'author'       => 'المؤلف',
        'version'      => 'الإصدار',
        'dependencies' => 'الإضافات المطلوبة',
        'dependents'   => 'الإضافات التي تعتمد على هذه',
        'is_installed' => 'حالة التثبيت',
        'license'      => 'الترخيص',
        'summary'      => 'الوصف',

        'dependencies-repeater' => [
            'title'        => 'الإضافات المطلوبة',
            'name'         => 'اسم الإضافة',
            'is_installed' => 'مُثبَّت',
            'placeholder'  => 'لا توجد تبعيات مطلوبة',
        ],

        'dependents-repeater' => [
            'title'        => 'الإضافات التي تعتمد على هذه',
            'name'         => 'اسم الإضافة',
            'is_installed' => 'مُثبَّت',
            'placeholder'  => 'لا توجد إضافات تابعة',
        ],

    ],

    'names' => [
        'accounting'     => 'المحاسبة',
        'accounts'       => 'الحسابات',
        'analytics'      => 'التحليلات',
        'barcode'        => 'الباركود',
        'blogs'          => 'المدونات',
        'chatter'        => 'المحادثات',
        'contacts'       => 'جهات الاتصال',
        'employees'      => 'الموظفون',
        'fields'         => 'الحقول المخصصة',
        'full-calendar'  => 'التقويم',
        'inventories'    => 'المخزون',
        'invoices'       => 'الفواتير',
        'maintenance'    => 'الصيانة',
        'manufacturing'  => 'التصنيع',
        'partners'       => 'الشركاء',
        'payments'       => 'المدفوعات',
        'plugin-manager' => 'مدير الإضافات',
        'products'       => 'المنتجات',
        'projects'       => 'المشاريع',
        'purchases'      => 'المشتريات',
        'recruitments'   => 'التوظيف',
        'sales'          => 'المبيعات',
        'security'       => 'الأمان',
        'support'        => 'الدعم',
        'table-views'    => 'عروض الجداول',
        'time-off'       => 'الإجازات',
        'timesheets'     => 'سجلات الدوام',
        'website'        => 'الموقع الإلكتروني',
    ],

    'summaries' => [
        'accounting'     => 'إدارة شجرة الحسابات واليوميات والقيود المالية',
        'accounts'       => 'إدارة الحسابات الأساسية والإعدادات المالية',
        'analytics'      => 'تقارير ولوحات معلومات لرؤى الأعمال',
        'barcode'        => 'تطبيق عمليات الباركود للمخزون والتصنيع',
        'blogs'          => 'إدارة المدونات',
        'chatter'        => 'سجل النشاط والرسائل والمتابعات على السجلات',
        'contacts'       => 'إدارة جهات الاتصال للعملاء والموردين',
        'employees'      => 'إدارة الموظفين',
        'fields'         => 'إضافة حقول مخصصة إلى الموارد',
        'full-calendar'  => 'عروض التقويم وجدولة الأحداث',
        'inventories'    => 'إدارة المخزون والمستودعات',
        'invoices'       => 'إنشاء الفواتير وإدارتها',
        'maintenance'    => 'إدارة الصيانة',
        'manufacturing'  => 'إدارة التصنيع والإنتاج',
        'partners'       => 'إدارة شركاء الأعمال',
        'payments'       => 'إدارة المدفوعات والمعاملات',
        'plugin-manager' => 'مدير الإضافات لنظام Aureus ERP',
        'products'       => 'إدارة كتالوج المنتجات والمتغيرات',
        'projects'       => 'تخطيط المشاريع وإدارتها',
        'purchases'      => 'إدارة المشتريات وأوامر الشراء',
        'recruitments'   => 'تتبع المتقدمين والتوظيف',
        'sales'          => 'إدارة مسار المبيعات والفرص',
        'security'       => 'الأدوار والصلاحيات والتحكم في الوصول',
        'support'        => 'دعم العملاء وإدارة التذاكر',
        'table-views'    => 'عروض جداول محفوظة وقابلة للتخصيص',
        'time-off'       => 'إدارة الإجازات وتتبعها',
        'timesheets'     => 'تتبع ساعات عمل الموظفين',
        'website'        => 'موقع إلكتروني للعملاء',
    ],

];
