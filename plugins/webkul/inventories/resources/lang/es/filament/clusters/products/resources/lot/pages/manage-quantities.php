<?php

return [
    'title' => 'Ubicaciones',

    'table' => [
        'columns' => [
            'product'           => 'Producto',
            'location'          => 'Ubicación',
            'storage-category'  => 'Categoría de Almacenamiento',
            'quantity'          => 'Cantidad',
            'package'           => 'Paquete',
            'on-hand'           => 'Cantidad En Existencia',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cantidad eliminada',
                    'body'  => 'La cantidad ha sido eliminada exitosamente.',
                ],
            ],
        ],
    ],
];
