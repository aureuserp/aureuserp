<?php

return [
    'title' => 'Capacidad por Paquetes',

    'form' => [
        'package-type' => 'Tipo de Paquete',
        'qty'          => 'Cantidad',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Agregar Capacidad de Tipo de Paquete',

                'notification' => [
                    'title' => 'Capacidad de Tipo de Paquete creada',
                    'body'  => 'La capacidad del tipo de paquete ha sido añadida exitosamente.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Tipo de Paquete',
            'qty'          => 'Cantidad',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacidad de Tipo de Paquete actualizada',
                    'body'  => 'La capacidad del tipo de paquete ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacidad de Tipo de Paquete eliminada',
                    'body'  => 'La capacidad del tipo de paquete ha sido eliminada exitosamente.',
                ],
            ],
        ],
    ],
];
