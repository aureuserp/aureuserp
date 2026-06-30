<?php

return [
    'title' => 'Ubicaciones',

    'table' => [
        'columns' => [
            'product'          => 'Producto',
            'location'         => 'Ubicación',
            'storage-category' => 'Categoría de almacenamiento',
            'quantity'         => 'Cantidad',
            'package'          => 'Paquete',
            'on-hand'          => 'Cantidad disponible',
            'unit'             => 'Unidad',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cantidad eliminada',
                    'body'  => 'La cantidad ha sido eliminada correctamente.',
                ],
            ],
        ],
    ],
];
