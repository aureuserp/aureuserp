<?php

return [
    'title' => 'Products',

    'table' => [
        'columns' => [
            'product'         => 'Product',
            'variants'        => 'Variants',
            'sales-price'     => 'Sales Price',
            'cost-price'      => 'Cost Price',
            'on-hand'         => 'On Hand',
            'forecast'        => 'Forecast',
            'unit-of-measure' => 'Unit of Measure',
        ],

        'groups' => [
            'product-type'  => 'Product Type',
            'category'      => 'Category',
            'uncategorized' => 'Uncategorized',
        ],

        'filters' => [
            'type'          => 'Product Type',
            'favorite'      => 'Favorite',
            'favorite-true' => 'Favorite',
            'favorite-false'=> 'Not Favorite',
            'favorite-all'  => 'All',
            'archived'      => 'Archived',
        ],
    ],

    'tabs' => [
        'default'  => 'Default',
        'goods'    => 'Goods',
        'services' => 'Services',
        'favorite' => 'Favorite',
        'archived' => 'Archived',
    ],

    'variants-infolist' => [
        'name'        => 'Variant Name',
        'sales-price' => 'Sales Price',
        'cost-price'  => 'Cost Price',
        'on-hand'     => 'On Hand',
        'forecast'    => 'Forecast',
        'unit'        => 'Unit',
        'variants'    => 'Variants',
        'close'       => 'Close',
    ],
];
