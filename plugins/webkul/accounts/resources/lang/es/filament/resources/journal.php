<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Asientos contables',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Información contable',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Secuencia dedicada para notas de crédito',
                            'dedicated-payment-sequence'     => 'Secuencia dedicada para pagos',
                            'sort-code-placeholder'          => 'Introducir el código del diario',
                            'sort-code'                      => 'Ordenar',
                            'currency'                       => 'Moneda',
                            'color'                          => 'Color',
                            'default-account'                => 'Cuenta predeterminada',
                            'profit-account'                 => 'Cuenta de ganancias',
                            'loss-account'                   => 'Cuenta de pérdidas',
                            'suspense-account'               => 'Cuenta transitoria',
                            'bank-account'                   => 'Cuenta bancaria',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Número de cuenta bancaria',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Pagos entrantes',
                'add-action-label' => 'Añadir línea',

                'fields' => [
                    'payment-method'             => 'Método de pago',
                    'display-name'               => 'Nombre para mostrar',
                    'account-number'             => 'Cuentas de cobros pendientes',
                    'relation-notes'             => 'Notas de relación',
                    'relation-notes-placeholder' => 'Introducir cualquier detalle de la relación',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Pagos salientes',
                'add-action-label' => 'Añadir línea',

                'fields' => [
                    'payment-method'             => 'Método de pago',
                    'display-name'               => 'Nombre para mostrar',
                    'account-number'             => 'Cuentas de pagos pendientes',
                    'relation-notes'             => 'Notas de relación',
                    'relation-notes-placeholder' => 'Introducir cualquier detalle de la relación',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Configuración avanzada',

                'fields' => [
                    'allowed-accounts'       => 'Cuentas permitidas',
                    'control-access'         => 'Control de acceso',
                    'payment-communication'  => 'Comunicación de pago',
                    'auto-check-on-post'     => 'Verificar automáticamente al contabilizar',
                    'communication-type'     => 'Tipo de comunicación',
                    'communication-standard' => 'Estándar de comunicación',
                ],
            ],
        ],

        'general' => [
            'title' => 'Información general',

            'fields' => [
                'name'    => 'Nombre',
                'type'    => 'Tipo',
                'company' => 'Empresa',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'type'       => 'Tipo',
            'code'       => 'Código',
            'currency'   => 'Moneda',
            'created-by' => 'Creado por',
            'status'     => 'Estado',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Diario eliminado',
                        'body'  => 'El diario se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'Error al eliminar el diario',
                        'body'  => 'El diario no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Diario eliminado',
                        'body'  => 'El diario se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'Error al eliminar los diarios',
                        'body'  => 'Los diarios no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Asientos contables',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Información contable',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Secuencia dedicada para notas de crédito',
                            'dedicated-payment-sequence'     => 'Secuencia dedicada para pagos',
                            'sort-code-placeholder'          => 'Introducir el código del diario',
                            'sort-code'                      => 'Ordenar',
                            'currency'                       => 'Moneda',
                            'color'                          => 'Color',
                            'default-account'                => 'Cuenta predeterminada',
                            'profit-account'                 => 'Cuenta de ganancias',
                            'loss-account'                   => 'Cuenta de pérdidas',
                            'suspense-account'               => 'Cuenta transitoria',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Número de cuenta bancaria',

                        'entries' => [
                            'account-number' => 'Número de cuenta',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Pagos entrantes',

                'entries' => [
                    'payment-method'             => 'Método de pago',
                    'display-name'               => 'Nombre para mostrar',
                    'account-number'             => 'Cuentas de cobros pendientes',
                    'relation-notes'             => 'Notas de relación',
                    'relation-notes-placeholder' => 'Introducir cualquier detalle de la relación',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Pagos salientes',

                'entries' => [
                    'payment-method'             => 'Método de pago',
                    'display-name'               => 'Nombre para mostrar',
                    'account-number'             => 'Cuentas de pagos pendientes',
                    'relation-notes'             => 'Notas de relación',
                    'relation-notes-placeholder' => 'Introducir cualquier detalle de la relación',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Configuración avanzada',

                'allowed-accounts' => [
                    'title' => 'Cuentas permitidas',

                    'entries' => [
                        'allowed-accounts'       => 'Cuentas permitidas',
                        'control-access'         => 'Control de acceso',
                        'auto-check-on-post'     => 'Verificar automáticamente al contabilizar',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Comunicación de pago',

                    'entries' => [
                        'communication-type'     => 'Tipo de comunicación',
                        'communication-standard' => 'Estándar de comunicación',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Información general',

            'entries' => [
                'name'    => 'Nombre',
                'type'    => 'Tipo',
                'company' => 'Empresa',
            ],
        ],
    ],

];
