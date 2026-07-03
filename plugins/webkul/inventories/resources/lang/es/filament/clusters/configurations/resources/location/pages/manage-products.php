<?php

return [
    'title' => 'Productos',

    'table' => [
        'columns' => [
            'product'         => 'Producto',
            'variants'        => 'Variantes',
            'sales-price'     => 'Precio de venta',
            'cost-price'      => 'Precio de costo',
            'on-hand'         => 'Disponible',
            'forecast'        => 'Previsión',
            'unit-of-measure' => 'Unidad de medida',
        ],

        'groups' => [
            'product-type'  => 'Tipo de producto',
            'category'      => 'Categoría',
            'uncategorized' => 'Sin categoría',
        ],

        'filters' => [
            'type'           => 'Tipo de producto',
            'favorite'       => 'Favorito',
            'favorite-true'  => 'Favorito',
            'favorite-false' => 'No favorito',
            'favorite-all'   => 'Todos',
            'archived'       => 'Archivados',
        ],
    ],

    'tabs' => [
        'default'  => 'Predeterminado',
        'goods'    => 'Bienes',
        'services' => 'Servicios',
        'favorite' => 'Favorito',
        'archived' => 'Archivados',
    ],

    'variants-infolist' => [
        'name'        => 'Nombre de la variante',
        'sales-price' => 'Precio de venta',
        'cost-price'  => 'Precio de costo',
        'on-hand'     => 'Disponible',
        'forecast'    => 'Previsión',
        'unit'        => 'Unidad',
        'variants'    => 'Variantes',
        'close'       => 'Cerrar',
    ],
];
