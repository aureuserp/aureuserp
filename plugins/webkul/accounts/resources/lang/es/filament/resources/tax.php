<?php

return [
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
                    'title' => 'Opciones Avanzadas',

                    'fields' => [
                        'invoice-label'       => 'Etiqueta de Factura',
                        'tax-group'           => 'Grupo de Impuestos',
                        'country'             => 'País',
                        'include-in-price'    => 'Incluido en Precio',
                        'include-base-amount' => 'Afectar Base de Impuestos Posteriores',
                        'is-base-affected'    => 'Base Afectada por Impuestos Anteriores',
                    ],
                ],

                'fields' => [
                    'description' => 'Descripción',
                    'legal-notes' => 'Notas Legales',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Nombre',
            'amount-type'            => 'Tipo de Monto',
            'company'                => 'Empresa',
            'tax-group'              => 'Grupo de Impuestos',
            'country'                => 'País',
            'tax-type'               => 'Tipo de Impuesto',
            'tax-scope'              => 'Ámbito del Impuesto',
            'invoice-label'          => 'Etiqueta de Factura',
            'tax-exigibility'        => 'Exigibilidad del Impuesto',
            'price-include-override' => 'Anulación de Inclusión de Precio',
            'amount'                 => 'Monto',
            'status'                 => 'Estado',
            'include-base-amount'    => 'Incluir Monto Base',
            'is-base-affected'       => 'Base Afectada',
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
                    'success' => [
                        'title' => 'Impuesto eliminado',
                        'body'  => 'El impuesto ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el Impuesto',
                        'body'  => 'El impuesto no puede ser eliminado porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Impuestos eliminados',
                        'body'  => 'Los impuestos han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los Impuestos',
                        'body'  => 'Los impuestos no pueden ser eliminados porque están en uso actualmente.',
                    ],
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
                    'title' => 'Opciones Avanzadas',

                    'entries' => [
                        'invoice-label'       => 'Etiqueta de Factura',
                        'tax-group'           => 'Grupo de Impuestos',
                        'country'             => 'País',
                        'include-in-price'    => 'Incluido en precio',
                        'include-base-amount' => 'Incluir monto base',
                        'is-base-affected'    => 'Base afectada',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Descripción y Notas Legales de Factura',
                    'entries' => [
                        'description' => 'Descripción',
                        'legal-notes' => 'Notas Legales',
                    ],
                ],
            ],
        ],
    ],

];
