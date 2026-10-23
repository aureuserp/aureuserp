<?php

return [
    'model-label'        => 'خطة متكررة',
    'plural-model-label' => 'الخطط المتكررة',

    'navigation' => [
        'title' => 'الخطط المتكررة',
    ],

    'header-actions' => [
        'create' => 'خطة متكررة جديدة',
    ],

    'form' => [
        'name'             => 'اسم الخطة',
        'name-placeholder' => 'مثل شهريًا',
        'months'           => 'عدد الأشهر',
        'months-helper'    => 'عدد الأشهر التي يغطيها الإيراد المتكرر (شهري = 1، سنوي = 12).',
    ],

    'table' => [
        'columns' => [
            'name'          => 'الخطة',
            'months'        => 'الأشهر',
            'opportunities' => 'الفرص',
        ],
    ],

    'actions' => [
        'archive'             => 'أرشفة',
        'archive-heading'     => 'أرشفة الخطة المتكررة',
        'archive-description' => 'تُنقل الخطة إلى المؤرشف. تحتفظ بها الفرص؛ يمكن استعادتها من تبويب المؤرشف.',
    ],

    'tabs' => [
        'active'   => 'نشط',
        'archived' => 'مؤرشف',
    ],

    'notifications' => [
        'created'  => 'تم إنشاء الخطة المتكررة',
        'updated'  => 'تم تحديث الخطة المتكررة',
        'archived' => 'تمت أرشفة الخطة المتكررة',
        'restored' => 'تمت استعادة الخطة المتكررة',
        'deleted'  => 'تم حذف الخطة المتكررة',
    ],

    'opportunity' => [
        'recurring-revenue' => 'الإيراد المتكرر',
        'plan'              => 'الخطة المتكررة',
        'mrr'               => 'الإيراد المتكرر الشهري',
    ],
];
