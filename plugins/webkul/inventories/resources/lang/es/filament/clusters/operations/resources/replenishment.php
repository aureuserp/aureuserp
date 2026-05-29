<?php

return [
    'navigation' => [
        'title' => 'Reabastecimiento',
        'group' => 'Aprovisionamiento',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Producto',
            'location'          => 'Ubicación',
            'route'             => 'Ruta',
            'vendor'            => 'Proveedor',
            'trigger'           => 'Disparador',
            'on-hand'           => 'Disponible',
            'min'               => 'Mín',
            'max'               => 'Máx',
            'multiple-quantity' => 'Cantidad múltiple',
            'to-order'          => 'Por pedir',
            'uom'               => 'UdM',
            'company'           => 'Empresa',
        ],

        'groups' => [
            'location' => 'Ubicación',
            'product'  => 'Producto',
            'category' => 'Categoría',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Agregar reabastecimiento',

                'notification' => [
                    'title' => 'Reabastecimiento agregado',
                    'body'  => 'El reabastecimiento ha sido agregado exitosamente.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'El reabastecimiento ya existe',
                        'body'  => 'Ya existe un reabastecimiento para esta configuración. Por favor, actualice el reabastecimiento existente.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
