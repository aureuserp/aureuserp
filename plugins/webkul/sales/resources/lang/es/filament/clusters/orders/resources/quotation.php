<?php

return [
    'title' => 'Presupuesto',

    'navigation' => [
        'title' => 'Presupuestos',
    ],

    'global-search' => [
        'customer'  => 'Cliente',
        'reference' => 'Referencia',
        'amount'    => 'Importe',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'customer'       => 'Cliente',
                    'expiration'     => 'Vencimiento',
                    'quotation-date' => 'Fecha del presupuesto',
                    'order-date'     => 'Fecha del pedido',
                    'payment-term'   => 'Plazo de pago',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Línea de pedido',

                'repeater' => [
                    'products' => [
                        'title'       => 'Productos',
                        'add-product' => 'Añadir producto',

                        'columns'     => [
                            'product'                    => 'Producto',
                            'product-variants'           => 'Variantes de producto',
                            'product-simple'             => 'Producto simple',
                            'quantity'                   => 'Cantidad',
                            'insufficient-stock-tooltip' => 'Stock insuficiente para satisfacer esta demanda.',
                            'uom'                        => 'UdM',
                            'lead-time'                  => 'Plazo de entrega',
                            'qty-delivered'              => 'Entregado',
                            'qty-invoiced'               => 'Facturado',
                            'packaging-qty'              => 'Cantidad de empaquetado',
                            'packaging'                  => 'Empaquetado',
                            'unit-price'                 => 'Precio unitario',
                            'cost'                       => 'Coste',
                            'margin'                     => 'Margen',
                            'taxes'                      => 'Impuestos',
                            'amount'                     => 'Importe',
                            'margin-percentage'          => 'Margen (%)',
                            'discount-percentage'        => 'Descuento (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Producto',
                            'product-variants'    => 'Variantes de producto',
                            'product-simple'      => 'Producto simple',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de medida',
                            'lead-time'           => 'Plazo de entrega',
                            'qty-delivered'       => 'Cantidad entregada',
                            'qty-invoiced'        => 'Cantidad facturada',
                            'packaging-qty'       => 'Cantidad de empaquetado',
                            'packaging'           => 'Empaquetado',
                            'unit-price'          => 'Precio unitario',
                            'cost'                => 'Coste',
                            'margin'              => 'Margen',
                            'taxes'               => 'Impuestos',
                            'amount'              => 'Importe',
                            'margin-percentage'   => 'Margen (%)',
                            'discount-percentage' => 'Descuento (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'No se puede reducir la cantidad',
                                'body'  => 'No se puede reducir la cantidad por debajo de la cantidad entregada (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'No se puede eliminar el producto',
                                'body'  => 'No se pueden eliminar productos de un pedido de venta confirmado.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Abrir producto',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Productos opcionales',
                        'add-product' => 'Añadir producto',

                        'columns' => [
                            'product'             => 'Producto',
                            'description'         => 'Descripción',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de medida',
                            'unit-price'          => 'Precio unitario',
                            'discount-percentage' => 'Descuento (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Producto',
                            'description'         => 'Descripción',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de medida',
                            'unit-price'          => 'Precio unitario',
                            'discount-percentage' => 'Descuento (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Añadir línea de pedido',
                                    'already-added' => 'Ya añadido al pedido',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Producto añadido',
                                        'body'  => 'El producto se ha añadido correctamente.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Producto no encontrado',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'El producto ya existe',
                                        'body'  => 'Este producto ya está en las líneas de pedido. Actualice la línea existente en su lugar.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Faltan datos del producto',
                                        'body'  => 'No se puede procesar el producto seleccionado.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Otra información',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Ventas',

                        'fields' => [
                            'sales-person'       => 'Comercial',
                            'customer-reference' => 'Referencia del cliente',
                            'tags'               => 'Etiquetas',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Envío',
                        'fields' => [
                            'warehouse'       => 'Almacén',
                            'commitment-date' => 'Fecha de entrega',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Seguimiento',
                        'fields' => [
                            'source-document' => 'Documento de origen',
                            'medium'          => 'Medio',
                            'source'          => 'Origen',
                            'campaign'        => 'Campaña',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Información adicional',

                        'fields' => [
                            'company'  => 'Empresa',
                            'currency' => 'Moneda',
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
        'columns' => [
            'number'             => 'Número',
            'status'             => 'Estado',
            'delivery-status'    => 'Estado de entrega',
            'invoice-status'     => 'Estado de factura',
            'creation-date'      => 'Fecha de creación',
            'commitment-date'    => 'Fecha de compromiso',
            'expected-date'      => 'Fecha prevista',
            'customer'           => 'Cliente',
            'sales-person'       => 'Comercial',
            'sales-team'         => 'Equipo de ventas',
            'untaxed-amount'     => 'Importe sin impuestos',
            'amount-tax'         => 'Importe de impuestos',
            'amount-total'       => 'Importe total',
            'customer-reference' => 'Referencia del cliente',
        ],

        'summarizers' => [
            'total'        => 'Total',
            'taxes'        => 'Impuestos',
            'total-amount' => 'Importe total',
        ],

        'filters' => [
            'sales-person'     => 'Comercial',
            'utm-source'       => 'Origen UTM',
            'company'          => 'Empresa',
            'customer'         => 'Cliente',
            'journal'          => 'Diario',
            'invoice-address'  => 'Dirección de factura',
            'shipping-address' => 'Dirección de envío',
            'fiscal-position'  => 'Posición fiscal',
            'payment-term'     => 'Plazo de pago',
            'currency'         => 'Moneda',
            'created-at'       => 'Creado el',
            'updated-at'       => 'Actualizado el',
        ],

        'groups' => [
            'medium'          => 'Medio',
            'source'          => 'Origen',
            'team'            => 'Equipo',
            'sales-person'    => 'Comercial',
            'currency'        => 'Moneda',
            'company'         => 'Empresa',
            'customer'        => 'Cliente',
            'quotation-date'  => 'Fecha del presupuesto',
            'commitment-date' => 'Fecha de compromiso',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Presupuesto restaurado',
                    'body'  => 'El presupuesto se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Presupuesto eliminado',
                    'body'  => 'El presupuesto se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Presupuesto eliminado permanentemente',
                    'body'  => 'El presupuesto se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Presupuestos restaurados',
                    'body'  => 'Los presupuestos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Presupuestos eliminados',
                    'body'  => 'Los presupuestos se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Presupuestos eliminados permanentemente',
                    'body'  => 'Los presupuestos se han eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Presupuestos creados',
                    'body'  => 'Los presupuestos se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'sale-order'     => 'Pedido de venta',
                    'customer'       => 'Cliente',
                    'expiration'     => 'Vencimiento',
                    'quotation-date' => 'Fecha del presupuesto',
                    'payment-term'   => 'Plazo de pago',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Línea de pedido',

                'repeater' => [
                    'products' => [
                        'title'       => 'Productos',
                        'add-product' => 'Añadir producto',
                        'entries'     => [
                            'product'             => 'Producto',
                            'product-variants'    => 'Variantes de producto',
                            'product-simple'      => 'Producto simple',
                            'quantity'            => 'Cantidad',
                            'qty-delivered'       => 'Entregado',
                            'qty-invoiced'        => 'Facturado',
                            'uom'                 => 'UdM',
                            'lead-time'           => 'Plazo de entrega',
                            'packaging-qty'       => 'Cantidad de empaquetado',
                            'packaging'           => 'Empaquetado',
                            'unit-price'          => 'Precio unitario',
                            'cost'                => 'Coste',
                            'margin'              => 'Margen',
                            'taxes'               => 'Impuestos',
                            'amount'              => 'Importe',
                            'margin-percentage'   => 'Margen (%)',
                            'discount-percentage' => 'Descuento (%)',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Productos opcionales',
                        'add-product' => 'Añadir producto',
                        'entries'     => [
                            'product'             => 'Producto',
                            'description'         => 'Descripción',
                            'quantity'            => 'Cantidad',
                            'uom'                 => 'Unidad de medida',
                            'unit-price'          => 'Precio unitario',
                            'discount-percentage' => 'Descuento (%)',
                            'sub-total'           => 'Subtotal',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Añadir línea de pedido',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Producto añadido',
                                        'body'  => 'El producto se ha añadido correctamente.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Otra información',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Ventas',

                        'entries' => [
                            'sales-person'       => 'Comercial',
                            'customer-reference' => 'Referencia del cliente',
                            'tags'               => 'Etiquetas',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Envío',
                        'entries' => [
                            'commitment-date' => 'Fecha de entrega',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Seguimiento',
                        'entries' => [
                            'source-document' => 'Documento de origen',
                            'medium'          => 'Medio',
                            'source'          => 'Origen',
                            'campaign'        => 'Campaña',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Información adicional',

                        'entries' => [
                            'company'  => 'Empresa',
                            'currency' => 'Moneda',
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
