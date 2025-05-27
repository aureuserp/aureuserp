<?php

return [
    'title' => 'Etiquetas de Cuenta',

    'navigation' => [
        'title' => 'Etiquetas de Cuenta',
        'group' => 'Contabilidad',
    ],

    'global-search' => [
        'country' => 'País',
        'name'    => 'Nombre',
    ],

    'form' => [
        'fields' => [
            'color'         => 'Color',
            'country'       => 'País',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'tax-negate'    => 'Negar Impuesto',
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
            'tax-negate'    => 'Negar Impuesto',
            'created-at'    => 'Creado En',
            'updated-at'    => 'Actualizado En',
            'deleted-at'    => 'Eliminado En',
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
                    'title' => 'Etiqueta de Cuenta actualizada',
                    'body'  => 'La etiqueta de Cuenta ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de Cuenta eliminada',
                    'body'  => 'La etiqueta de Cuenta ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas de Cuenta eliminadas',
                    'body'  => 'Las etiquetas de Cuenta han sido eliminadas exitosamente.',
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
            'tax-negate'    => 'Negar Impuesto',
        ],
    ],
];
