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

    'tabs' => [
        'active'   => 'نشط',
        'archived' => 'مؤرشف',
    ],

    'notifications' => [
        'created'  => 'تم إنشاء المستوى',
        'updated'  => 'تم تحديث المستوى',
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
