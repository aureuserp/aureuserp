<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Color',
            'country'       => 'País',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'tax-negate'    => 'Negar impuesto',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Color',
            'country'       => 'País',
            'created-by'    => 'Creado por',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'tax-negate'    => 'Negar impuesto',
            'created-at'    => 'Creado el',
            'updated-at'    => 'Actualizado el',
            'deleted-at'    => 'Eliminado el',
        ],

        'filters' => [
            'bank'           => 'Banco',
            'account-holder' => 'Titular de la cuenta',
            'creator'        => 'Creador',
            'can-send-money' => 'Puede enviar dinero',
        ],

        'groups' => [
            'country'       => 'País',
            'created-by'    => 'Creado por',
            'applicability' => 'Aplicabilidad',
            'name'          => 'Nombre',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta de cuenta actualizada',
                    'body'  => 'La etiqueta de cuenta se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de cuenta eliminada',
                    'body'  => 'La etiqueta de cuenta se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas de cuenta eliminadas',
                    'body'  => 'Las etiquetas de cuenta se han eliminado correctamente.',
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
            'tax-negate'    => 'Negar impuesto',
        ],
    ],
];
