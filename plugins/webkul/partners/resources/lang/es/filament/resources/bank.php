<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'  => 'Nombre',
                    'code'  => 'Código Identificador de Banco',
                    'email' => 'Email',
                    'phone' => 'Teléfono',
                ],
            ],

            'address' => [
                'title' => 'Dirección',

                'fields' => [
                    'address' => 'Dirección',
                    'city'    => 'Ciudad',
                    'street1' => 'Calle 1',
                    'street2' => 'Calle 2',
                    'state'   => 'Provincia',
                    'zip'     => 'Código Postal',
                    'country' => 'País',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'code'       => 'Código Identificador de Banco',
            'country'    => 'País',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
            'deleted-at' => 'Eliminado El',
        ],

        'groups' => [
            'country'    => 'País',
            'created-at' => 'Creado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Banco actualizado',
                    'body'  => 'El banco ha sido actualizado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Banco restaurado',
                    'body'  => 'El banco ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banco eliminado',
                    'body'  => 'El banco ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banco eliminado permanentemente',
                    'body'  => 'El banco ha sido eliminado permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bancos restaurados',
                    'body'  => 'Los bancos han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bancos eliminados',
                    'body'  => 'Los bancos han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bancos eliminados permanentemente',
                    'body'  => 'Los bancos han sido eliminados permanentemente exitosamente.',
                ],
            ],
        ],
    ],
];
