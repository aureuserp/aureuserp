<?php

return [
    'title' => 'Impuestos',

    'navigation' => [
        'title' => 'Impuestos',
        'group' => 'Contabilidad',
    ],

    'global-search' => [
        'company'     => 'Empresa',
        'amount-type' => 'Tipo de Monto',
        'name'        => 'Nombre',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'            => 'Nombre',
                'tax-type'        => 'Tipo de Impuesto',
                'tax-computation' => 'Cálculo del Impuesto',
                'tax-scope'       => 'Ámbito del Impuesto',
                'status'          => 'Estado',
                'amount'          => 'Monto',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title'  => 'Opciones Avanzadas',
                    'fields' => [
                        'invoice-label'       => 'Etiqueta de la factura',
                        'tax-group'           => 'Grupo de Impuestos',
                        'country'             => 'País',
                        'include-in-price'    => 'Incluir en el precio',
                        'include-base-amount' => 'Incluir importe base',
                        'is-base-affected'    => '¿Afecta al importe base?',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'  => 'Descripción y Notas Legales de la Factura',
                    'fields' => [
                        'description' => 'Descripción',
                        'legal-notes' => 'Notas Legales',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'amount-type'          => 'Tipo de Monto',
            'company'              => 'Empresa',
            'tax-group'            => 'Grupo de Impuestos',
            'country'              => 'País',
            'type-tax-use'         => 'Tipo de Uso del Impuesto',
            'tax-scope'            => 'Ámbito del Impuesto',
            'invoice-label'        => 'Etiqueta de la Factura',
            'tax-exigibility'      => 'Exigibilidad del Impuesto',
            'price-include-override' => 'Anular Inclusión en Precio',
            'amount'               => 'Monto',
            'status'               => 'Estado',
            'include-base-amount'  => 'Incluir Importe Base',
            'is-base-affected'     => '¿Afecta al Importe Base?',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'company'      => 'Empresa',
            'tax-group'    => 'Grupo de Impuestos',
            'country'      => 'País',
            'created-by'   => 'Creado Por',
            'type-tax-use' => 'Tipo de Uso del Impuesto',
            'tax-scope'    => 'Ámbito del Impuesto',
            'amount-type'  => 'Tipo de Monto',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Condición de pago eliminada',
                    'body'  => 'La condición de pago ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Impuestos eliminados',
                    'body'  => 'Los impuestos han sido eliminados exitosamente.',
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

];
