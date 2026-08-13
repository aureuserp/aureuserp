<?php

return [
    'title' => 'Asientos contables',

    'navigation' => [
        'title' => 'Asientos contables',
    ],

    'record-sub-navigation' => [
        'payment' => 'Pago',
    ],

    'global-search' => [
        'number'   => 'Número',
        'partner'  => 'Contacto',
        'date'     => 'Fecha de factura',
        'due-date' => 'Fecha de vencimiento de factura',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'reference'       => 'Referencia',
                    'accounting-date' => 'Fecha contable',
                    'journal'         => 'Diario',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Apuntes contables',

                'repeater' => [
                    'title'       => 'Apuntes',
                    'add-item'    => 'Agregar apunte',

                    'columns' => [
                        'account'                  => 'Cuenta',
                        'partner'                  => 'Contacto',
                        'label'                    => 'Etiqueta',
                        'amount-currency'          => 'Importe (moneda)',
                        'currency'                 => 'Moneda',
                        'taxes'                    => 'Impuestos',
                        'debit'                    => 'Débito',
                        'credit'                   => 'Crédito',
                        'discount-amount-currency' => 'Importe de descuento (moneda)',
                    ],

                    'fields' => [
                        'account'                  => 'Cuenta',
                        'partner'                  => 'Contacto',
                        'label'                    => 'Etiqueta',
                        'amount-currency'          => 'Importe (moneda)',
                        'currency'                 => 'Moneda',
                        'taxes'                    => 'Impuestos',
                        'debit'                    => 'Débito',
                        'credit'                   => 'Crédito',
                        'discount-amount-currency' => 'Importe de descuento (moneda)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Otra información',

                'fields' => [
                    'checked'         => 'Verificado',
                    'company'         => 'Empresa',
                    'fiscal-position' => 'Posición fiscal',
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Total',
        'columns' => [
            'invoice-date' => 'Fecha de factura',
            'date'         => 'Fecha',
            'number'       => 'Número',
            'partner'      => 'Contacto',
            'reference'    => 'Referencia',
            'journal'      => 'Diario',
            'company'      => 'Empresa',
            'total'        => 'Total',
            'state'        => 'Estado',
            'checked'      => 'Verificado',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'partner'        => 'Contacto',
            'journal'        => 'Diario',
            'state'          => 'Estado',
            'payment-method' => 'Método de pago',
            'date'           => 'Fecha',
            'invoice-date'   => 'Fecha de factura',
            'company'        => 'Empresa',
        ],

        'filters' => [
            'number'                       => 'Número',
            'invoice-partner-display-name' => 'Nombre del contacto de la factura',
            'invoice-date'                 => 'Fecha de factura',
            'invoice-due-date'             => 'Fecha de vencimiento de factura',
            'invoice-origin'               => 'Origen de la factura',
            'reference'                    => 'Referencia',
            'created-at'                   => 'Creado el',
            'updated-at'                   => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pago eliminado',
                    'body'  => 'El pago se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagos eliminados',
                    'body'  => 'Los pagos se han eliminado correctamente.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'number'          => 'Número',
                    'reference'       => 'Referencia',
                    'accounting-date' => 'Fecha contable',
                    'journal'         => 'Diario',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Apuntes contables',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Cuenta',
                        'partner'  => 'Contacto',
                        'label'    => 'Etiqueta',
                        'currency' => 'Moneda',
                        'taxes'    => 'Impuestos',
                        'debit'    => 'Débito',
                        'credit'   => 'Crédito',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Otra información',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Contabilidad',

                        'entries' => [
                            'company'         => 'Empresa',
                            'fiscal-position' => 'Posición fiscal',
                            'checked'         => 'Verificado',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],

];
