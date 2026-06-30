<?php

return [
    'title' => 'Factura',

    'navigation' => [
        'title' => 'Facturas',
        'group' => 'Facturas',
    ],

    'global-search' => [
        'customer' => 'Cliente',
        'date'     => 'Fecha',
        'due-date' => 'Fecha de vencimiento',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'customer-invoice' => 'Factura de cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de factura',
                    'due-date'         => 'Fecha de vencimiento',
                    'payment-term'     => 'Condición de pago',
                    'journal'          => 'Diario',
                    'currency'         => 'Moneda',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de factura',

                'repeater' => [
                    'products' => [
                        'title'       => 'Productos',
                        'add-product' => 'Agregar producto',

                        'columns' => [
                            'product'             => 'Producto',
                            'quantity'            => 'Cantidad',
                            'unit'                => 'Unidad',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Descuento',
                            'unit-price'          => 'Precio unitario',
                            'sub-total'           => 'Subtotal',
                        ],

                        'fields' => [
                            'product'             => 'Producto',
                            'quantity'            => 'Cantidad',
                            'unit'                => 'Unidad',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Porcentaje de descuento',
                            'unit-price'          => 'Precio unitario',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Otra información',

                'fieldset' => [
                    'invoice' => [
                        'title'  => 'Factura',

                        'fields' => [
                            'customer-reference' => 'Referencia del cliente',
                            'sales-person'       => 'Vendedor',
                            'payment-reference'  => 'Referencia de pago',
                            'recipient-bank'     => 'Banco del destinatario',
                            'delivery-date'      => 'Fecha de entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fields' => [
                            'company'                 => 'Empresa',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Ubicación del Incoterm',
                            'fiscal-position'         => 'Posición fiscal',
                            'fiscal-position-tooltip' => 'Las posiciones fiscales se utilizan para adaptar los impuestos y las cuentas según la ubicación del cliente.',
                            'cash-rounding'           => 'Método de redondeo de efectivo',
                            'cash-rounding-tooltip'   => 'Especifica la unidad pagable en efectivo más pequeña de la moneda.',
                            'payment-method'          => 'Método de pago',
                            'auto-post'               => 'Contabilización automática',
                            'checked'                 => 'Verificado',
                        ],
                    ],
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
            'number'           => 'Número',
            'state'            => 'Estado',
            'created-by'       => 'Creado por',
            'customer'         => 'Cliente',
            'invoice-date'     => 'Fecha de factura',
            'checked'          => 'Verificado',
            'accounting-date'  => 'Contabilidad',
            'due-date'         => 'Fecha de vencimiento',
            'source-document'  => 'Documento de origen',
            'reference'        => 'Referencia',
            'sales-person'     => 'Vendedor',
            'tax-excluded'     => 'Impuestos excluidos',
            'tax'              => 'Impuesto',
            'total'            => 'Total',
            'amount-due'       => 'Importe pendiente',
            'invoice-currency' => 'Moneda de la factura',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Nombre',
            'invoice-partner-display-name' => 'Nombre del contacto de la factura',
            'invoice-date'                 => 'Fecha de factura',
            'checked'                      => 'Verificado',
            'date'                         => 'Fecha',
            'invoice-due-date'             => 'Fecha de vencimiento de la factura',
            'invoice-origin'               => 'Origen de la factura',
            'sales-person'                 => 'Vendedor',
            'currency'                     => 'Moneda',
            'created-at'                   => 'Creado el',
            'updated-at'                   => 'Actualizado el',
        ],

        'filters' => [
            'number'                       => 'Número',
            'invoice-partner-display-name' => 'Nombre del contacto de la factura',
            'invoice-date'                 => 'Fecha de factura',
            'invoice-due-date'             => 'Fecha de vencimiento de la factura',
            'invoice-origin'               => 'Origen de la factura',
            'reference'                    => 'Referencia',
            'payment-reference'            => 'Referencia de pago',
            'narration'                    => 'Narración',
            'partner'                      => 'Contacto',
            'journal'                      => 'Diario',
            'fiscal-position'              => 'Posición fiscal',
            'currency'                     => 'Moneda',
            'company'                      => 'Empresa',
            'date'                         => 'Fecha contable',
            'delivery-date'                => 'Fecha de entrega',
            'amount-untaxed'               => 'Importe sin impuestos',
            'amount-tax'                   => 'Importe de impuestos',
            'amount-total'                 => 'Importe total',
            'amount-residual'              => 'Importe pendiente',
            'checked'                      => 'Verificado',
            'posted-before'                => 'Contabilizado antes',
            'is-move-sent'                 => 'Enviado',
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
                    'customer-invoice' => 'Factura de cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de factura',
                    'due-date'         => 'Fecha de vencimiento',
                    'payment-term'     => 'Condición de pago',
                    'journal'          => 'Diario',
                    'currency'         => 'Moneda',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de factura',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Producto',
                            'quantity'            => 'Cantidad',
                            'unit'                => 'Unidad de medida',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Porcentaje de descuento',
                            'unit-price'          => 'Precio unitario',
                            'sub-total'           => 'Subtotal',
                            'total'               => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Otra información',

                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Factura',

                        'entries' => [
                            'customer-reference' => 'Referencia del cliente',
                            'sales-person'       => 'Vendedor',
                            'payment-reference'  => 'Referencia de pago',
                            'recipient-bank'     => 'Banco del destinatario',
                            'delivery-date'      => 'Fecha de entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'entries' => [
                            'company'           => 'Empresa',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación del Incoterm',
                            'payment-method'    => 'Método de pago',
                            'cash-rounding'     => 'Método de redondeo de efectivo',
                            'fiscal-position'   => 'Posición fiscal',
                            'auto-post'         => 'Contabilización automática',
                            'checked'           => 'Verificado',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Términos y condiciones',
            ],

            'journal-items' => [
                'title' => 'Apuntes contables',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Cuenta',
                        'partner'  => 'Contacto',
                        'label'    => 'Etiqueta',
                        'currency' => 'Moneda',
                        'due-date' => 'Fecha de vencimiento',
                        'taxes'    => 'Impuestos',
                        'debit'    => 'Débito',
                        'credit'   => 'Crédito',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Agregar',
            ],

            'unreconcile' => [
                'label' => 'Desvincular',
            ],
        ],
    ],

];
