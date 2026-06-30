<?php

return [
    'title' => 'Capacidad por paquetes',

    'form' => [
        'package-type' => 'Tipo de paquete',
        'qty'          => 'Cantidad',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Agregar capacidad por tipo de paquete',

                'notification' => [
                    'title' => 'Capacidad por tipo de paquete creada',
                    'body'  => 'La capacidad por tipo de paquete ha sido agregada correctamente.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Tipo de paquete',
            'qty'          => 'Cantidad',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacidad por tipo de paquete actualizada',
                    'body'  => 'La capacidad por tipo de paquete ha sido actualizada correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacidad por tipo de paquete eliminada',
                    'body'  => 'La capacidad por tipo de paquete ha sido eliminada correctamente.',
                ],
            ],
        ],
    ],
];
