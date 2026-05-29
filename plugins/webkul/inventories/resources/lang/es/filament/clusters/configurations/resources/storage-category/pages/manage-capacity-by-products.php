<?php

return [
    'title' => 'Capacidad por productos',

    'form' => [
        'product' => 'Producto',
        'qty'     => 'Cantidad',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Agregar capacidad por producto',

                'notification' => [
                    'title' => 'Capacidad por producto creada',
                    'body'  => 'La capacidad por producto ha sido agregada exitosamente.',
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
                    'title' => 'Capacidad por producto actualizada',
                    'body'  => 'La capacidad por producto ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacidad por producto eliminada',
                    'body'  => 'La capacidad por producto ha sido eliminada exitosamente.',
                ],
            ],
        ],
    ],
];
