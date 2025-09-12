<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Color',
            'country'       => 'País',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'tax-negate'    => 'Anular Impuesto',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Color',
            'country'       => 'País',
            'created-by'    => 'Creado Por',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'tax-negate'    => 'Anular Impuesto',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
            'deleted-at'    => 'Eliminado El',
        ],

        'filters' => [
            'bank'           => 'Banco',
            'account-holder' => 'Titular de la Cuenta',
            'creator'        => 'Creador',
            'can-send-money' => 'Puede Enviar Dinero',
        ],

        'groups' => [
            'country'       => 'País',
            'created-by'    => 'Creado Por',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta de cuenta actualizada',
                    'body'  => 'La etiqueta de cuenta ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de cuenta eliminada',
                    'body'  => 'La etiqueta de cuenta ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas de cuenta eliminadas',
                    'body'  => 'Las etiquetas de cuenta han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Color',
            'country'       => 'País',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'tax-negate'    => 'Anular Impuesto',
        ],
    ],
];
