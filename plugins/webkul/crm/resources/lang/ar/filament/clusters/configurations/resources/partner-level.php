<?php

return [
    'model-label'        => 'مستوى',
    'plural-model-label' => 'المستويات',

    'navigation' => [
        'title' => 'المستويات',
        'group' => 'الأعضاء',
    ],

    'header-actions' => [
        'create' => 'مستوى جديد',
    ],

    'form' => [
        'name'          => 'الاسم',
        'weight'        => 'وزن المستوى',
        'weight-helper' => 'مدى احتمال حصول شركاء هذا المستوى على عملاء محتملين. 0 يعني لا شيء.',
    ],

    'table' => [
        'columns' => [
            'name'     => 'الاسم',
            'weight'   => 'وزن المستوى',
            'partners' => 'جهات الاتصال',
        ],
    ],

    'actions' => [
        'archive'             => 'أرشفة',
        'archive-heading'     => 'أرشفة المستوى',
        'archive-description' => 'يُنقل المستوى إلى المؤرشف. تحتفظ به جهات الاتصال؛ يمكن استعادته من تبويب المؤرشف.',
    ],

    'tabs' => [
        'active'   => 'نشط',
        'archived' => 'مؤرشف',
    ],

    'notifications' => [
        'created'  => 'تم إنشاء المستوى',
        'updated'  => 'تم تحديث المستوى',
        'archived' => 'تمت أرشفة المستوى',
        'restored' => 'تمت استعادة المستوى',
        'deleted'  => 'تم حذف المستوى',
    ],

    'contact' => [
        'section'       => 'مستوى الشريك',
        'level'         => 'المستوى',
        'weight'        => 'وزن المستوى',
        'weight-helper' => 'اتركه فارغًا لاستخدام وزن المستوى.',
    ],
];
