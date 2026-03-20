<?php

return [

    'navigation' => [
        'group' => 'الإضافات',
    ],

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

];
