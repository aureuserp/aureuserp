<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name'            => 'Nombre',
                'tax-type'        => 'Tipo de impuesto',
                'tax-computation' => 'Cálculo del impuesto',
                'tax-scope'       => 'Ámbito del impuesto',
                'status'          => 'Estado',
                'amount'          => 'Importe',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Líneas de reparto de factura',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Líneas de reparto de reembolso',
                ],

                'fields' => [
                    'type'           => 'Tipo',
                    'factor-percent' => 'Factor %',
                    'account'        => 'Cuenta',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opciones avanzadas',

                    'fields' => [
                        'invoice-label'       => 'Etiqueta de factura',
                        'tax-group'           => 'Grupo de impuestos',
                        'country'             => 'País',
                        'include-in-price'    => 'Incluido en el precio',
                        'include-base-amount' => 'Afecta a la base de impuestos posteriores',
                        'is-base-affected'    => 'Base afectada por impuestos anteriores',
                    ],
                ],

                'fields' => [
                    'description' => 'Descripción',
                    'legal-notes' => 'Notas legales',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Nombre',
            'amount-type'            => 'Tipo de importe',
            'company'                => 'Empresa',
            'tax-group'              => 'Grupo de impuestos',
            'country'                => 'País',
            'tax-type'               => 'Tipo de impuesto',
            'tax-scope'              => 'Ámbito del impuesto',
            'amount-type'            => 'Tipo de importe',
            'invoice-label'          => 'Etiqueta de factura',
            'tax-exigibility'        => 'Exigibilidad del impuesto',
            'price-include-override' => 'Anulación de inclusión en precio',
            'amount'                 => 'Importe',
            'status'                 => 'Estado',
            'include-base-amount'    => 'Incluir importe base',
            'is-base-affected'       => 'Base afectada',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'company'      => 'Empresa',
            'tax-group'    => 'Grupo de impuestos',
            'country'      => 'País',
            'created-by'   => 'Creado por',
            'type-tax-use' => 'Tipo de uso del impuesto',
            'tax-scope'    => 'Ámbito del impuesto',
            'amount-type'  => 'Tipo de importe',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Impuesto eliminado',
                        'body'  => 'El impuesto se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el impuesto',
                        'body'  => 'El impuesto no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Impuestos eliminados',
                        'body'  => 'Los impuestos se han eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los impuestos',
                        'body'  => 'Los impuestos no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Líneas de reparto no válidas',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Líneas de reparto no válidas',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Nombre',
                'tax-type'        => 'Tipo de impuesto',
                'tax-computation' => 'Cálculo del impuesto',
                'tax-scope'       => 'Ámbito del impuesto',
                'status'          => 'Estado',
                'amount'          => 'Importe',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opciones avanzadas',

                    'entries' => [
                        'invoice-label'       => 'Etiqueta de factura',
                        'tax-group'           => 'Grupo de impuestos',
                        'country'             => 'País',
                        'include-in-price'    => 'Incluir en el precio',
                        'include-base-amount' => 'Incluir importe base',
                        'is-base-affected'    => 'Base afectada',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Descripción y notas legales de la factura',
                    'entries' => [
                        'description' => 'Descripción',
                        'legal-notes' => 'Notas legales',
                    ],
                ],
            ],
        ],
    ],

];
