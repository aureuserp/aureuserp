<?php

return [
    'title' => 'Cotización',

    'navigation' => [
        'title' => 'Cotizaciones',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'customer'      => 'Cliente',
                    'expiration'    => 'Vencimiento',
                    'quotation-date' => 'Fecha de Cotización',
                    'order-date'     => 'Fecha de Pedido',
                    'payment-term'  => 'Término de Pago',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Línea de Pedido',

                'repeater' => [
                    'products' => [
                        'title'       => 'Productos',
                        'add-product' => 'Agregar Producto',
                        'fields'      => [
                            'product'             => 'Producto',
                            'product-variants'    => 'Variantes del Producto',
                            'product-simple'      => 'Producto Simple',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de Medida',
                            'lead-time'           => 'Plazo de Entrega',
                            'qty-delivered'       => 'Cantidad Entregada',
                            'qty-invoiced'        => 'Cantidad Facturada',
                            'packaging-qty'       => 'Cantidad de Embalaje',
                            'packaging'           => 'Embalaje',
                            'unit-price'          => 'Precio Unitario',
                            'cost'                => 'Costo',
                            'margin'              => 'Margen',
                            'taxes'               => 'Impuestos',
                            'amount'              => 'Importe',
                            'margin-percentage'   => 'Margen (%)',
                            'discount-percentage' => 'Descuento (%)',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Productos Opcionales',
                        'add-product' => 'Agregar Producto',
                        'fields'      => [
                            'product'             => 'Producto',
                            'description'         => 'Descripción',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de Medida',
                            'unit-price'          => 'Precio Unitario',
                            'discount-percentage' => 'Descuento (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Agregar Línea de Pedido',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Producto agregado',
                                        'body'  => 'El producto ha sido agregado exitosamente.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'other-information' => [
            'title' => 'Otra Información',

            'fieldset' => [
                'sales' => [
                    'title'  => 'Ventas',
                    'fields' => [
                        'sales-person'     => 'Vendedor',
                        'customer-reference' => 'Referencia del Cliente',
                        'tags'               => 'Etiquetas',
                    ],
                ],

                'shipping' => [
                    'title'  => 'Envío',
                    'fields' => [
                        'commitment-date' => 'Fecha de Entrega',
                    ],
                ],

                'tracking' => [
                    'title'  => 'Seguimiento',
                    'fields' => [
                        'source-document' => 'Documento de Origen',
                        'medium'          => 'Medio',
                        'source'          => 'Fuente',
                        'campaign'        => 'Campaña',
                    ],
                ],

                'additional-information' => [
                    'title'  => 'Información Adicional',
                    'fields' => [
                        'company'  => 'Empresa',
                        'currency' => 'Moneda',
                    ],
                ],
            ],
        ],

        'term-and-conditions' => [
            'title' => 'Términos y Condiciones',
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Número',
            'status'             => 'Estado',
            'invoice-status'     => 'Estado de Factura',
            'creation-date'      => 'Fecha de Creación',
            'commitment-date'    => 'Fecha de Entrega',
            'expected-date'      => 'Fecha Esperada',
            'customer'           => 'Cliente',
            'sales-person'       => 'Vendedor',
            'sales-team'         => 'Equipo de Ventas',
            'untaxed-amount'     => 'Importe Sin Impuestos',
            'amount-tax'         => 'Importe de Impuestos',
            'amount-total'       => 'Importe Total',
            'customer-reference' => 'Referencia del Cliente',
        ],

        'filters' => [
            'sales-person'     => 'Vendedor',
            'utm-source'       => 'Fuente UTM',
            'company'          => 'Empresa',
            'customer'         => 'Cliente',
            'journal'          => 'Diario',
            'invoice-address'  => 'Dirección de Facturación',
            'shipping-address' => 'Dirección de Envío',
            'fiscal-position'  => 'Posición Fiscal',
            'payment-term'     => 'Término de Pago',
            'currency'         => 'Moneda',
            'created-at'       => 'Creado En',
            'updated-at'       => 'Actualizado En',
        ],

        'groups' => [
            'medium'          => 'Medio',
            'source'          => 'Fuente',
            'team'            => 'Equipo',
            'sales-person'    => 'Vendedor',
            'currency'        => 'Moneda',
            'company'         => 'Empresa',
            'customer'        => 'Cliente',
            'quotation-date'  => 'Fecha de Cotización',
            'commitment-date' => 'Fecha de Entrega',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cotización restaurada',
                    'body'  => 'La cotización ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cotización eliminada',
                    'body'  => 'La cotización ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cotización eliminada permanentemente',
                    'body'  => 'La cotización ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cotizaciones restauradas',
                    'body'  => 'Las cotizaciones han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cotizaciones eliminadas',
                    'body'  => 'Las cotizaciones han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cotizaciones eliminadas permanentemente',
                    'body'  => 'Las cotizaciones han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Cotizaciones creadas',
                    'body'  => 'Las cotizaciones han sido creadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'customer'      => 'Cliente',
                    'expiration'    => 'Vencimiento',
                    'quotation-date' => 'Fecha de Cotización',
                    'payment-term'  => 'Término de Pago',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Línea de Pedido',

                'repeater' => [
                    'products' => [
                        'title'       => 'Productos',
                        'add-product' => 'Agregar Producto',
                        'entries'     => [
                            'product'             => 'Producto',
                            'product-variants'    => 'Variantes del Producto',
                            'product-simple'      => 'Producto Simple',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de Medida',
                            'lead-time'           => 'Plazo de Entrega',
                            'packaging-qty'       => 'Cantidad de Embalaje',
                            'packaging'           => 'Embalaje',
                            'unit-price'          => 'Precio Unitario',
                            'cost'                => 'Costo',
                            'margin'              => 'Margen',
                            'taxes'               => 'Impuestos',
                            'amount'              => 'Importe',
                            'margin-percentage'   => 'Margen (%)',
                            'discount-percentage' => 'Descuento (%)',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Productos Opcionales',
                        'add-product' => 'Agregar Producto',
                        'entries'     => [
                            'product'             => 'Producto',
                            'description'         => 'Descripción',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de Medida',
                            'unit-price'          => 'Precio Unitario',
                            'discount-percentage' => 'Descuento (%)',
                            'sub-total'           => 'Subtotal',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Agregar Línea de Pedido',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Producto agregado',
                                        'body'  => 'El producto ha sido agregado exitosamente.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'other-information' => [
            'title'   => 'Otra Información',
            'entries' => [
                'sales-person'     => 'Vendedor',
                'customer-reference' => 'Referencia del Cliente',
                'tags'               => 'Etiquetas',
                'commitment-date'    => 'Fecha de Entrega',
                'source-document'    => 'Documento de Origen',
                'medium'             => 'Medio',
                'source'             => 'Fuente',
                'campaign'           => 'Campaña',
                'company'            => 'Empresa',
                'currency'           => 'Moneda',
            ],
        ],

        'term-and-conditions' => [
            'title' => 'Términos y Condiciones',
        ],
    ],
];
