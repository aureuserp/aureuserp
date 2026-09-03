<?php

return [
    'title' => 'Cantidades',

    'tabs' => [
        'internal-locations' => 'Ubicaciones internas',
        'transit-locations'  => 'Ubicaciones en tránsito',
        'on-hand'            => 'Disponible',
        'to-count'           => 'Por contar',
        'to-apply'           => 'Por aplicar',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Producto',
            'location'         => 'Ubicación',
            'package'          => 'Paquete',
            'lot'              => 'Lote / Números de serie',
            'on-hand-qty'      => 'Cantidad disponible',
            'storage-category' => 'Categoría de almacenamiento',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Producto',
            'location'          => 'Ubicación',
            'lot'               => 'Lote / Números de serie',
            'storage-category'  => 'Categoría de almacenamiento',
            'quantity'          => 'Cantidad',
            'package'           => 'Paquete',
            'on-hand'           => 'Cantidad disponible',
            'unit'              => 'Unidad',
            'reserved-quantity' => 'Cantidad reservada',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Cantidad actualizada',
                    'body'  => 'La cantidad ha sido actualizada correctamente.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Agregar cantidad',

                'notification' => [
                    'title' => 'Cantidad agregada',
                    'body'  => 'La cantidad ha sido agregada correctamente.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'La cantidad ya existe',
                        'body'  => 'Ya existe una cantidad para la misma configuración. Por favor, actualice la cantidad existente.',
                    ],
                ],
            ],
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
