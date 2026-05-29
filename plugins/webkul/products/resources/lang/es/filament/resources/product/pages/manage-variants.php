<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date'                   => 'Fecha',
        'employee'               => 'Empleado',
        'description'            => 'Descripción',
        'time-spent'             => 'Tiempo empleado',
        'time-spent-helper-text' => 'Tiempo empleado en horas (Ej. 1.5 horas significa 1 hora 30 minutos)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Fecha',
            'employee'               => 'Empleado',
            'description'            => 'Descripción',
            'time-spent'             => 'Tiempo empleado',
            'time-spent-on-subtasks' => 'Tiempo empleado en subtareas',
            'total-time-spent'       => 'Tiempo total empleado',
            'remaining-time'         => 'Tiempo restante',
            'variant-values'         => 'Valores de variante',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante eliminada',
                    'body'  => 'La variante ha sido eliminada correctamente.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
                    'print' => [
                        'label' => 'Imprimir etiquetas',

                        'form' => [
                            'fields' => [
                                'quantity' => 'Número de etiquetas',
                                'format'   => 'Formato',

                                'format-options' => [
                                    'dymo'       => 'Dymo',
                                    '2x7_price'  => '2x7 con precio',
                                    '4x7_price'  => '4x7 con precio',
                                    '4x12'       => '4x12',
                                    '4x12_price' => '4x12 con precio',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
