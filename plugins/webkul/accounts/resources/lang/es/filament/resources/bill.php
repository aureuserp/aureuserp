<?php

return [
    'title' => 'Factura',

    'navigation' => [
        'title' => 'Facturas',
        'group' => 'Facturas',
    ],

    'global-search' => [
        'vendor'   => 'Proveedor',
        'date'     => 'Fecha',
        'due-date' => 'Fecha de vencimiento',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'vendor-bill'       => 'Factura de proveedor',
                    'vendor'            => 'Proveedor',
                    'bill-date'         => 'Fecha de la factura',
                    'bill-reference'    => 'Referencia de la factura',
                    'accounting-date'   => 'Fecha contable',
                    'payment-reference' => 'Referencia de pago',
                    'recipient-bank'    => 'Banco del destinatario',
                    'due-date'          => 'Fecha de vencimiento',
                    'payment-term'      => 'Condición de pago',
                    'journal'           => 'Diario',
                    'currency'          => 'Moneda',
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
                            'discount-percentage' => 'Porcentaje de descuento',
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
                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fields' => [
                            'company'                 => 'Empresa',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Ubicación del Incoterm',
                            'payment-method'          => 'Método de pago',
                            'fiscal-position'         => 'Posición fiscal',
                            'fiscal-position-tooltip' => 'Las posiciones fiscales se utilizan para adaptar los impuestos y las cuentas según la ubicación del cliente.',
                            'cash-rounding'           => 'Método de redondeo de efectivo',
                            'cash-rounding-tooltip'   => 'Especifica la unidad más pequeña de la moneda que se puede pagar en efectivo.',
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
            'customer'         => 'Cliente',
            'bill-date'        => 'Fecha de la factura',
            'checked'          => 'Verificado',
            'accounting-date'  => 'Contabilidad',
            'due-date'         => 'Fecha de vencimiento',
            'source-document'  => 'Documento de origen',
            'reference'        => 'Referencia',
            'sales-person'     => 'Comercial',
            'tax-excluded'     => 'Impuestos excluidos',
            'tax'              => 'Impuesto',
            'total'            => 'Total',
            'amount-due'       => 'Importe pendiente',
            'bill-currency'    => 'Moneda de la factura',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Nombre',
            'bill-partner-display-name'    => 'Nombre mostrado del contacto de la factura',
            'bill-date'                    => 'Fecha de la factura',
            'checked'                      => 'Verificado',
            'date'                         => 'Fecha',
            'bill-due-date'                => 'Fecha de vencimiento de la factura',
            'bill-origin'                  => 'Origen de la factura',
            'sales-person'                 => 'Comercial',
            'currency'                     => 'Moneda',
            'created-at'                   => 'Creado el',
            'updated-at'                   => 'Actualizado el',
        ],

        'filters' => [
            'number'                    => 'Número',
            'bill-partner-display-name' => 'Nombre mostrado del contacto de la factura',
            'bill-date'                 => 'Fecha de la factura',
            'bill-due-date'             => 'Fecha de vencimiento de la factura',
            'bill-origin'               => 'Origen de la factura',
            'reference'                 => 'Referencia',
            'payment-reference'         => 'Referencia de pago',
            'narration'                 => 'Anotación',
            'partner'                   => 'Contacto',
            'journal'                   => 'Diario',
            'fiscal-position'           => 'Posición fiscal',
            'currency'                  => 'Moneda',
            'company'                   => 'Empresa',
            'date'                      => 'Fecha contable',
            'delivery-date'             => 'Fecha de entrega',
            'amount-untaxed'            => 'Importe sin impuestos',
            'amount-tax'                => 'Importe de impuestos',
            'amount-total'              => 'Importe total',
            'amount-residual'           => 'Importe pendiente',
            'checked'                   => 'Verificado',
            'posted-before'             => 'Contabilizado antes',
            'is-move-sent'              => 'Enviado',
            'created-at'                => 'Creado el',
            'updated-at'                => 'Actualizado el',
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
                    'vendor-invoice'    => 'Factura de proveedor',
                    'vendor'            => 'Proveedor',
                    'bill-date'         => 'Fecha de la factura',
                    'bill-reference'    => 'Referencia de la factura',
                    'accounting-date'   => 'Fecha contable',
                    'payment-reference' => 'Referencia de pago',
                    'recipient-bank'    => 'Banco del destinatario',
                    'due-date'          => 'Fecha de vencimiento',
                    'payment-term'      => 'Condición de pago',
                    'journal'           => 'Diario',
                    'currency'          => 'Moneda',
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

                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Contabilidad',

                        'entries' => [
                            'company'           => 'Empresa',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación del Incoterm',
                            'payment-method'    => 'Método de pago',
                            'checked'           => 'Verificado',
                            'fiscal-position'   => 'Posición fiscal',
                            'cash-rounding'     => 'Método de redondeo de efectivo',
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
                        'due-date' => 'Fecha de vencimiento',
                        'currency' => 'Moneda',
                        'taxes'    => 'Impuestos',
                        'debit'    => 'Débito',
                        'credit'   => 'Crédito',
                    ],
                ],
            ],
        ],
    ],
];
