<?php

return [
    'navigation' => [
        'title' => 'Informes',
        'group' => 'Inventario',
    ],

    'moves' => [
        'navigation' => [
            'title' => 'Historial de movimientos',
        ],

        'filters' => [
            'product-category'     => 'Categoría de producto',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'package'              => 'Paquete',
            'lot'                  => 'Número de lote / serie',
            'package-type'         => 'Tipo de paquete',
        ],

        'groups' => [
            'product'   => 'Producto',
            'status'    => 'Estado',
            'date'      => 'Fecha',
            'operation' => 'Operación',
            'location'  => 'Ubicación',
            'category'  => 'Categoría',
        ],
    ],

    'quantities' => [
        'navigation' => [
            'title' => 'Ubicaciones',
        ],

        'filters' => [
            'warehouse'        => 'Almacén',
            'location'         => 'Ubicación',
            'product-category' => 'Categoría de producto',
            'storage-category' => 'Categoría de almacenamiento',
            'package'          => 'Paquete',
            'lot'              => 'Número de lote / serie',
            'package-type'     => 'Tipo de paquete',
        ],

        'groups' => [
            'product'          => 'Producto',
            'product-category' => 'Categoría de producto',
            'location'         => 'Ubicación',
            'storage-category' => 'Categoría de almacenamiento',
            'lot'              => 'Número de lote / serie',
            'package'          => 'Paquete',
            'company'          => 'Empresa',
        ],
    ],
];
