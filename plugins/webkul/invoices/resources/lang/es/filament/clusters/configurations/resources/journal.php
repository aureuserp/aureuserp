<?php

return [
    'title' => 'Diario',

    'navigation' => [
        'title' => 'Diario',
        'group' => 'Contabilidad',
    ],

    'global-search' => [
        'name' => 'Nombre',
        'code' => 'Código',
    ],

    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title'     => 'Asientos de Diario',
                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Información Contable',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Secuencia Dedicada de Notas de Crédito',
                            'dedicated-payment-sequence'     => 'Secuencia Dedicada de Pagos',
                            'sort-code-placeholder'          => 'Ingrese el código del diario',
                            'sort-code'                      => 'Ordenar',
                            'currency'                       => 'Moneda',
                            'color'                          => 'Color',
                        ],
                    ],
                    'bank-account-number'    => [
                        'title' => 'Número de Cuenta Bancaria',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title'  => 'Pagos Recibidos',
                'fields' => [
                    'relation-notes'           => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de la relación',
                ],
            ],
            'outgoing-payments' => [
                'title'  => 'Pagos Emitidos',
                'fields' => [
                    'relation-notes'           => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de la relación',
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
            'title'  => 'Información General',
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
                    'title' => 'Incoterm eliminado',
                    'body'  => 'El incoterm ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Diario eliminado',
                    'body'  => 'El diario ha sido eliminado exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title'     => 'Asientos de Diario',
                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Información Contable',
                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Secuencia Dedicada de Notas de Crédito',
                            'dedicated-payment-sequence'     => 'Secuencia Dedicada de Pagos',
                            'sort-code-placeholder'          => 'Ingrese el código del diario',
                            'sort-code'                      => 'Ordenar',
                            'currency'                       => 'Moneda',
                            'color'                          => 'Color',
                        ],
                    ],
                    'bank-account-number'    => [
                        'title' => 'Número de Cuenta Bancaria',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title'   => 'Pagos Recibidos',
                'entries' => [
                    'relation-notes'           => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de la relación',
                ],
            ],
            'outgoing-payments' => [
                'title'   => 'Pagos Emitidos',
                'entries' => [
                    'relation-notes'           => 'Notas de Relación',
                    'relation-notes-placeholder' => 'Ingrese cualquier detalle de la relación',
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
            'title'   => 'Información General',
            'entries' => [
                'name'    => 'Nombre',
                'type'    => 'Tipo',
                'company' => 'Empresa',
            ],
        ],
    ],

];
