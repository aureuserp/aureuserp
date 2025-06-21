<?php

return [
    'title' => 'Factura',

    'navigation' => [
        'title' => 'Facturas',
        'group' => 'Facturas',
    ],

    'global-search' => [
        'number'           => 'Número',
        'customer'         => 'Cliente',
        'invoice-date'     => 'Fecha de Factura',
        'invoice-due-date' => 'Fecha de Vencimiento de Factura',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'customer-invoice' => 'Factura de Cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de Factura',
                    'due-date'         => 'Fecha de Vencimiento',
                    'payment-term'     => 'Condición de Pago',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de Factura',

                'repeater' => [
                    'products' => [
                        'title'       => 'Productos',
                        'add-product' => 'Agregar Producto',

                        'fields' => [
                            'product'             => 'Producto',
                            'quantity'            => 'Cantidad',
                            'unit'                => 'Unidad',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Porcentaje de Descuento',
                            'unit-price'          => 'Precio Unitario',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Otra Información',
                'fieldset' => [
                    'invoice' => [
                        'title'  => 'Factura',
                        'fields' => [
                            'customer-reference' => 'Referencia del Cliente',
                            'sales-person'       => 'Vendedor',
                            'payment-reference'  => 'Referencia de Pago',
                            'recipient-bank'     => 'Banco del Receptor',
                            'delivery-date'      => 'Fecha de Entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación Incoterm',
                            'payment-method'    => 'Método de Pago',
                            'auto-post'         => 'Publicación Automática',
                            'checked'           => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Información Adicional',
                        'fields' => [
                            'company'  => 'Empresa',
                            'currency' => 'Moneda',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campaña',
                            'medium'   => 'Medio',
                            'source'   => 'Origen',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Total',
        'columns' => [
            'number'           => 'Número',
            'state'            => 'Estado',
            'customer'         => 'Cliente',
            'invoice-date'     => 'Fecha de Factura',
            'checked'          => 'Verificado',
            'accounting-date'  => 'Fecha Contable',
            'due-date'         => 'Fecha de Vencimiento',
            'source-document'  => 'Documento de Origen',
            'reference'        => 'Referencia',
            'sales-person'     => 'Vendedor',
            'tax-excluded'     => 'Impuestos Excluidos',
            'tax'              => 'Impuesto',
            'total'            => 'Total',
            'amount-due'       => 'Monto Adeudado',
            'invoice-currency' => 'Moneda de la Factura',
        ],

        'groups' => [
            'name'                         => 'Nombre',
            'invoice-partner-display-name' => 'Nombre Visible del Socio de la Factura',
            'invoice-date'                 => 'Fecha de Factura',
            'checked'                      => 'Verificado',
            'date'                         => 'Fecha',
            'invoice-due-date'             => 'Fecha de Vencimiento de Factura',
            'invoice-origin'               => 'Origen de la Factura',
            'sales-person'                 => 'Vendedor',
            'currency'                     => 'Moneda',
            'created-at'                   => 'Creado El',
            'updated-at'                   => 'Actualizado El',
        ],

        'filters' => [
            'number'                       => 'Número',
            'invoice-partner-display-name' => 'Nombre Visible del Socio de la Factura',
            'invoice-date'                 => 'Fecha de Factura',
            'invoice-due-date'             => 'Fecha de Vencimiento de Factura',
            'invoice-origin'               => 'Origen de la Factura',
            'reference'                    => 'Referencia',
            'created-at'                   => 'Creado El',
            'updated-at'                   => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Factura eliminada',
                    'body'  => 'La factura ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Facturas eliminadas',
                    'body'  => 'Las facturas han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'customer-invoice' => 'Factura de Cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de Factura',
                    'due-date'         => 'Fecha de Vencimiento',
                    'payment-term'     => 'Condición de Pago',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de Factura',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Producto',
                            'quantity'            => 'Cantidad',
                            'unit'                => 'Unidad de Medida',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Porcentaje de Descuento',
                            'unit-price'          => 'Precio Unitario',
                            'sub-total'           => 'Subtotal',
                            'total'               => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Otra Información',
                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Factura',
                        'entries' => [
                            'customer-reference' => 'Referencia del Cliente',
                            'sales-person'       => 'Vendedor',
                            'payment-reference'  => 'Referencia de Pago',
                            'recipient-bank'     => 'Banco del Receptor',
                            'delivery-date'      => 'Fecha de Entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación Incoterm',
                            'payment-method'    => 'Método de Pago',
                            'auto-post'         => 'Publicación Automática',
                            'checked'           => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Información Adicional',
                        'entries' => [
                            'company'  => 'Empresa',
                            'currency' => 'Moneda',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campaña',
                            'medium'   => 'Medio',
                            'source'   => 'Origen',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
    ],

];
