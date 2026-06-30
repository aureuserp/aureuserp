<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'  => 'Nombre',
                    'code'  => 'Código de identificación bancaria',
                    'email' => 'Correo electrónico',
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
                    'state'   => 'Estado',
                    'zip'     => 'Código postal',
                    'country' => 'País',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nombre',
            'code'           => 'Código de identificación bancaria',
            'country'        => 'País',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
            'deleted-at'     => 'Eliminado el',
        ],

        'groups' => [
            'country'               => 'País',
            'created-at'            => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Banco actualizado',
                    'body'  => 'El banco se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Banco restaurado',
                    'body'  => 'El banco se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banco eliminado',
                    'body'  => 'El banco se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banco eliminado permanentemente',
                    'body'  => 'El banco se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bancos restaurados',
                    'body'  => 'Los bancos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bancos eliminados',
                    'body'  => 'Los bancos se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bancos eliminados permanentemente',
                    'body'  => 'Los bancos se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],
];
