<?php

return [
    'navigation' => [
        'title' => 'Cuentas Bancarias',
        'group' => 'Bancos',
    ],

    'form' => [
        'account-number' => 'Número de Cuenta',
        'bank'           => [
            'title'    => 'Banco',
            'sections' => [
                'general' => [
                    'title' => 'General',

                    'fields' => [
                        'name'  => 'Nombre',
                        'code'  => 'Código de Identificación Bancaria (BIC)',
                        'email' => 'Correo Electrónico',
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
                        'state'   => 'Provincia/Estado',
                        'zip'     => 'Código Postal',
                        'country' => 'País',
                    ],
                ],
            ],
        ],

        'account-holder' => 'Titular de la Cuenta',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Número de Cuenta',
            'bank'           => 'Banco',
            'account-holder' => 'Titular de la Cuenta',
            'send-money'     => 'Puede Enviar Dinero',
            'created-at'     => 'Creado El',
            'updated-at'     => 'Actualizado El',
            'deleted-at'     => 'Eliminado El',
        ],

        'filters' => [
            'bank'           => 'Banco',
            'account-holder' => 'Titular de la Cuenta',
            'creator'        => 'Creador',
            'can-send-money' => 'Puede Enviar Dinero',
        ],

        'groups' => [
            'bank'           => 'Banco',
            'can-send-money' => 'Puede Enviar Dinero',
            'created-at'     => 'Creado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Cuenta bancaria actualizada',
                    'body'  => 'La cuenta bancaria ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Cuenta bancaria restaurada',
                    'body'  => 'La cuenta bancaria ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cuenta bancaria eliminada',
                    'body'  => 'La cuenta bancaria ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cuenta bancaria eliminada permanentemente',
                    'body'  => 'La cuenta bancaria ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cuentas bancarias restauradas',
                    'body'  => 'Las cuentas bancarias han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cuentas bancarias eliminadas',
                    'body'  => 'Las cuentas bancarias han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cuentas bancarias eliminadas permanentemente',
                    'body'  => 'Las cuentas bancarias han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],
];