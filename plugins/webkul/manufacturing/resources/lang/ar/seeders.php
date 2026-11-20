<?php

return [
    'locations' => [
        'pre-production'  => 'ما قبل الإنتاج',
        'post-production' => 'ما بعد الإنتاج',
    ],

    'operation-types' => [
        'pick-components'        => 'انتقاء المكونات',
        'store-finished-product' => 'تخزين المنتج النهائي',
        'manufacturing'          => 'التصنيع',
    ],

    'routes' => [
        'manufacture-one-step'    => ':warehouse: التصنيع (خطوة واحدة)',
        'manufacture-two-steps'   => ':warehouse: انتقاء المكونات ثم التصنيع (خطوتان)',
        'manufacture-three-steps' => ':warehouse: انتقاء المكونات والتصنيع ثم تخزين المنتجات (3 خطوات)',
    ],

    'rules' => [
        'stock-pre-production'      => ':code: المخزون → ما قبل الإنتاج',
        'pre-production-production' => ':code: ما قبل الإنتاج → الإنتاج',
        'post-production-stock'     => ':code: ما بعد الإنتاج → المخزون',
    ],
];
