<?php

return [
    'navigation' => [
        'title' => 'Cuentas bancarias',
        'group' => 'Bancos',
    ],

    'form' => [
        'account-number'     => 'Número de cuenta',
        'bank'               => [
            'title'    => 'Banco',
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
                        'state'   => 'Provincia',
                        'zip'     => 'Código postal',
                        'country' => 'País',
                    ],
                ],
            ],
        ],

        'account-holder'     => 'Titular de la cuenta',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Número de cuenta',
            'bank'           => 'Banco',
            'account-holder' => 'Titular de la cuenta',
            'send-money'     => 'Puede enviar dinero',
            'created-at'     => 'Creado el',
            'updated-at'     => 'Actualizado el',
            'deleted-at'     => 'Eliminado el',
        ],

        'filters' => [
            'bank'           => 'Banco',
            'account-holder' => 'Titular de la cuenta',
            'creator'        => 'Creador',
            'can-send-money' => 'Puede enviar dinero',
        ],

        'groups' => [
            'bank'               => 'Banco',
            'can-send-money'     => 'Puede enviar dinero',
            'created-at'         => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Cuenta bancaria actualizada',
                    'body'  => 'La cuenta bancaria se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Cuenta bancaria restaurada',
                    'body'  => 'La cuenta bancaria se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cuenta bancaria eliminada',
                    'body'  => 'La cuenta bancaria se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cuenta bancaria eliminada permanentemente',
                    'body'  => 'La cuenta bancaria se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cuentas bancarias restauradas',
                    'body'  => 'Las cuentas bancarias se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cuentas bancarias eliminadas',
                    'body'  => 'Las cuentas bancarias se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cuentas bancarias eliminadas permanentemente',
                    'body'  => 'Las cuentas bancarias se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],
];
