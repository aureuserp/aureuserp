<?php

return [
    'title' => 'Cuentas',

    'navigation' => [
        'title' => 'Cuentas',
        'group' => 'Contabilidad',
    ],

    'global-search' => [
        'currency' => 'Moneda',
        'name'     => 'Nombre',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'          => 'Código',
                'account-name'  => 'Nombre de la Cuenta',
                'accounting'    => 'Contabilidad',
                'account-type'  => 'Tipo de Cuenta',
                'default-taxes' => 'Impuestos Predeterminados',
                'tags'          => 'Etiquetas',
                'journals'      => 'Diarios',
                'currency'      => 'Moneda',
                'deprecated'    => 'Obsoleto',
                'reconcile'     => 'Conciliar',
                'non-trade'     => 'No Comercial',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'          => 'Código',
            'account-name'  => 'Nombre de la Cuenta',
            'account-type'  => 'Tipo de Cuenta',
            'currency'      => 'Moneda',
            'deprecated'    => 'Obsoleto',
            'reconcile'     => 'Conciliar',
            'non-trade'     => 'No Comercial',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cuenta eliminada',
                    'body'  => 'La cuenta ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cuentas eliminadas',
                    'body'  => 'Las cuentas han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Nombre',
                'tax-type'        => 'Tipo de Impuesto',
                'tax-computation' => 'Cálculo del Impuesto',
                'tax-scope'       => 'Ámbito del Impuesto',
                'status'          => 'Estado',
                'amount'          => 'Monto',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title'   => 'Opciones Avanzadas',
                    'entries' => [
                        'invoice-label'       => 'Etiqueta de la factura',
                        'tax-group'           => 'Grupo de Impuestos',
                        'country'             => 'País',
                        'include-in-price'    => 'Incluir en el precio',
                        'include-base-amount' => 'Incluir importe base',
                        'is-base-affected'    => '¿Afecta al importe base?',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Descripción y Notas Legales de la Factura',
                    'entries' => [
                        'description' => 'Descripción',
                        'legal-notes' => 'Notas Legales',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'          => 'Código',
                'account-name'  => 'Nombre de la Cuenta',
                'accounting'    => 'Contabilidad',
                'account-type'  => 'Tipo de Cuenta',
                'default-taxes' => 'Impuestos Predeterminados',
                'tags'          => 'Etiquetas',
                'journals'      => 'Diarios',
                'currency'      => 'Moneda',
                'deprecated'    => 'Obsoleto',
                'reconcile'     => 'Conciliar',
                'non-trade'     => 'No Comercial',
            ],
        ],
    ],
];
