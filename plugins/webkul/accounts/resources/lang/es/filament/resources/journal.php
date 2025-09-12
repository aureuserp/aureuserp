<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Asientos Contables',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Información Contable',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Secuencia Dedicada de Nota de Crédito',
                            'dedicated-payment-sequence'     => 'Secuencia Dedicada de Pago',
                            'sort-code-placeholder'          => 'Ingrese el código del diario',
                            'sort-code'                      => 'Código de Clasificación',
                            'currency'                       => 'Moneda',
                            'color'                          => 'Color',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Número de Cuenta Bancaria',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Pagos Entrantes',

                'fields' => [
                    'relation-notes'             => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de relación',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Pagos Salientes',

                'fields' => [
                    'relation-notes'             => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de relación',
                ],
            ],
            'advanced-settings' => [
                'title'  => 'Configuración Avanzada',
                'fields' => [
                    'allowed-accounts'       => 'Cuentas Permitidas',
                    'control-access'         => 'Control de Acceso',
                    'payment-communication'  => 'Comunicación de Pago',
                    'auto-check-on-post'     => 'Verificación Automática al Publicar',
                    'communication-type'     => 'Tipo de Comunicación',
                    'communication-standard' => 'Estándar de Comunicación',
                ],
            ],
        ],

        'general' => [
            'title' => 'Información General',

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
            'created-by' => 'Creado Por',
            'status'     => 'Estado',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Asiento contable eliminado',
                    'body'  => 'El asiento contable ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Asientos contables eliminados',
                    'body'  => 'Los asientos contables han sido eliminados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Asientos Contables',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Información Contable',
                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Secuencia Dedicada de Nota de Crédito',
                            'dedicated-payment-sequence'     => 'Secuencia Dedicada de Pago',
                            'sort-code-placeholder'          => 'Ingrese el código del diario',
                            'sort-code'                      => 'Código de Clasificación',
                            'currency'                       => 'Moneda',
                            'color'                          => 'Color',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Número de Cuenta Bancaria',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Pagos Entrantes',

                'entries' => [
                    'relation-notes'             => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de relación',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Pagos Salientes',

                'entries' => [
                    'relation-notes'             => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de relación',
                ],
            ],
            'advanced-settings' => [
                'title'   => 'Configuración Avanzada',
                'entries' => [
                    'allowed-accounts'       => 'Cuentas Permitidas',
                    'control-access'         => 'Control de Acceso',
                    'payment-communication'  => 'Comunicación de Pago',
                    'auto-check-on-post'     => 'Verificación Automática al Publicar',
                    'communication-type'     => 'Tipo de Comunicación',
                    'communication-standard' => 'Estándar de Comunicación',
                ],
            ],
        ],

        'general' => [
            'title' => 'Información General',

            'entries' => [
                'name'    => 'Nombre',
                'type'    => 'Tipo',
                'company' => 'Empresa',
            ],
        ],
    ],

];
