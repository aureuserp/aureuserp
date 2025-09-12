<?php

return [
    'navigation' => [
        'title' => 'Reabastecimiento',
        'group' => 'Adquisiciones',
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
            'on-hand'           => 'En Existencia',
            'min'               => 'Mín',
            'max'               => 'Máx',
            'multiple-quantity' => 'Cantidad Múltiple',
            'to-order'          => 'Para Pedir',
            'uom'               => 'UDM',
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
                'label' => 'Agregar Reabastecimiento',

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
