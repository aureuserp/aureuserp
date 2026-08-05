<?php

return [
    'model-label' => 'Secuencia',

    'plural-model-label' => 'Secuencias',

    'navigation' => [
        'title' => 'Secuencias',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'      => 'Nombre',
                    'code'      => 'Código',
                    'code-help' => 'Identificador técnico usado por los documentos, p. ej. sales.order. Las secuencias creadas automáticamente ya tienen el código correcto.',
                    'company'   => 'Compañía',
                ],
            ],

            'format' => [
                'title' => 'Numeración',

                'fields' => [
                    'prefix'          => 'Prefijo',
                    'prefix-help'     => 'Marcadores: %(year), %(y), %(month), %(day). Ejemplo: INV/%(year)/',
                    'suffix'          => 'Sufijo',
                    'padding'         => 'Relleno del Número',
                    'next-number'     => 'Próximo Número',
                    'step'            => 'Incremento',
                    'reset-frequency' => 'Reiniciar Contador',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nombre',
            'code'            => 'Código',
            'applies-to'      => 'Se Aplica A',
            'company'         => 'Compañía',
            'next-preview'    => 'Próximo Número de Documento',
            'next-number'     => 'Próximo Número',
            'reset-frequency' => 'Reiniciar Contador',
        ],

        'variants' => [
            'refund'         => 'Reembolso',
            'payment'        => 'Pago',
            'refund-payment' => 'Reembolso + Pago',
        ],

        'filters' => [
            'company' => 'Compañía',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Secuencia actualizada',
                    'body'  => 'La secuencia se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Secuencia eliminada',
                    'body'  => 'La secuencia se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Secuencias eliminadas',
                    'body'  => 'Las secuencias se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
