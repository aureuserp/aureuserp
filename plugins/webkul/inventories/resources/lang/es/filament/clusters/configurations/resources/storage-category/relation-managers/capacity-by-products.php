<?php

return [
    'title' => 'Capacidad por Productos',

    'form' => [
        'product' => 'Producto',
        'qty'     => 'Cantidad',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Agregar Capacidad de Producto',

                'notification' => [
                    'title' => 'Capacidad de Producto creada',
                    'body'  => 'La capacidad del producto ha sido añadida exitosamente.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Producto',
            'qty'     => 'Cantidad',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacidad de Producto actualizada',
                    'body'  => 'La capacidad del producto ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacidad de Producto eliminada',
                    'body'  => 'La capacidad del producto ha sido eliminada exitosamente.',
                ],
            ],
        ],
    ],
];
