<?php

return [
    'title' => 'المنتجات',

    'table' => [
        'columns' => [
            'product'         => 'المنتج',
            'variants'        => 'المتغيرات',
            'sales-price'     => 'سعر البيع',
            'cost-price'      => 'سعر التكلفة',
            'on-hand'         => 'المتوفر في اليد',
            'forecast'        => 'المتوقع',
            'unit-of-measure' => 'وحدة القياس',
        ],

        'groups' => [
            'product-type'  => 'نوع المنتج',
            'category'      => 'الفئة',
            'uncategorized' => 'غير مصنف',
        ],

        'filters' => [
            'type'           => 'نوع المنتج',
            'favorite'       => 'المفضلة',
            'favorite-true'  => 'المفضلة',
            'favorite-false' => 'غير مفضلة',
            'favorite-all'   => 'الكل',
            'archived'       => 'المؤرشفة',
        ],
    ],

    'tabs' => [
        'default'  => 'الافتراضي',
        'goods'    => 'السلع',
        'services' => 'الخدمات',
        'favorite' => 'المفضلة',
        'archived' => 'المؤرشفة',
    ],

    'variants-infolist' => [
        'name'        => 'اسم المتغير',
        'sales-price' => 'سعر البيع',
        'cost-price'  => 'سعر التكلفة',
        'on-hand'     => 'المتوفر في اليد',
        'forecast'    => 'المتوقع',
        'unit'        => 'الوحدة',
        'variants'    => 'المتغيرات',
        'close'       => 'إغلاق',
    ],
];
