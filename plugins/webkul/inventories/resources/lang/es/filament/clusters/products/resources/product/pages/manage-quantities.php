<?php

return [
    'title' => 'Cantidades',

    'tabs' => [
        'internal-locations' => 'Ubicaciones Internas',
        'transit-locations'  => 'Ubicaciones de Tránsito',
        'on-hand'            => 'En Existencia',
        'to-count'           => 'Para Contar',
        'to-apply'           => 'Para Aplicar',
    ],

    'form' => [
        'fields' => [
            'product'           => 'Producto',
            'location'          => 'Ubicación',
            'package'           => 'Paquete',
            'lot'               => 'Lote / Números de Serie',
            'on-hand-qty'       => 'Cantidad En Existencia',
            'storage-category'  => 'Categoría de Almacenamiento',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Producto',
            'location'          => 'Ubicación',
            'lot'               => 'Lote / Números de Serie',
            'storage-category'  => 'Categoría de Almacenamiento',
            'quantity'          => 'Cantidad',
            'package'           => 'Paquete',
            'on-hand'           => 'Cantidad En Existencia',
            'reserved-quantity' => 'Cantidad Reservada',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Cantidad actualizada',
                    'body'  => 'La cantidad ha sido actualizada exitosamente.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Agregar Cantidad',

                'notification' => [
                    'title' => 'Cantidad agregada',
                    'body'  => 'La cantidad ha sido agregada exitosamente.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'La cantidad ya existe',
                        'body'  => 'Ya existe una cantidad para la misma configuración. Por favor, actualice la cantidad en su lugar.',
                    ],
                ],
            ],
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
