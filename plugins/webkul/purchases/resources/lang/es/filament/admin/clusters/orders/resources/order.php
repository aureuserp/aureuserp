<?php

return [
    'global-search' => [
        'vendor'    => 'Proveedor',
        'reference' => 'Referencia',
        'amount'    => 'Importe',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'vendor'                   => 'Proveedor',
                    'vendor-reference'         => 'Referencia de proveedor',
                    'vendor-reference-tooltip' => 'El número de referencia del pedido de venta o de la oferta proporcionado por el proveedor. Se utiliza para la conciliación al recibir productos, ya que esta referencia suele incluirse en el albarán de entrega del proveedor.',
                    'agreement'                => 'Acuerdo',
                    'currency'                 => 'Moneda',
                    'confirmation-date'        => 'Fecha de confirmación',
                    'order-deadline'           => 'Fecha límite del pedido',
                    'expected-arrival'         => 'Llegada prevista',
                    'confirmed-by-vendor'      => 'Confirmado por el proveedor',
                    'deliver-to'               => 'Entregar a',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'repeater' => [
                    'products' => [
                        'title'            => 'Productos',
                        'add-product-line' => 'Añadir producto',

                        'fields' => [
                            'product'             => 'Producto',
                            'expected-arrival'    => 'Llegada prevista',
                            'quantity'            => 'Cantidad',
                            'received'            => 'Recibido',
                            'billed'              => 'Facturado',
                            'unit'                => 'Unidad',
                            'packaging-qty'       => 'Cantidad por embalaje',
                            'packaging'           => 'Embalaje',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Descuento (%)',
                            'unit-price'          => 'Precio unitario',
                            'amount'              => 'Importe',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'No se puede reducir la cantidad',
                                'body'  => 'No se puede reducir la cantidad por debajo de la cantidad recibida (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'La cantidad supera el límite del pedido abierto',
                                'body'  => 'La cantidad del producto (:product_qty) supera la cantidad disponible (:available_qty) del pedido abierto.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Producto',
                            'expected-arrival'    => 'Llegada prevista',
                            'quantity'            => 'Cantidad',
                            'received'            => 'Recibido',
                            'billed'              => 'Facturado',
                            'unit'                => 'Unidad',
                            'packaging-qty'       => 'Cantidad por embalaje',
                            'packaging'           => 'Embalaje',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Descuento (%)',
                            'unit-price'          => 'Precio unitario',
                            'amount'              => 'Importe',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'No se puede eliminar el producto',
                                'body'  => 'Los productos no se pueden eliminar de un pedido de compra confirmado.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Abrir producto',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Añadir sección',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Añadir nota',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Información adicional',

                'fields' => [
                    'buyer'             => 'Comprador',
                    'company'           => 'Empresa',
                    'source-document'   => 'Documento de origen',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Los términos comerciales internacionales (Incoterms) son un conjunto de términos comerciales estandarizados utilizados en transacciones globales para definir las responsabilidades entre compradores y vendedores.',
                    'incoterm-location' => 'Ubicación del Incoterm',
                    'payment-term'      => 'Condición de pago',
                    'fiscal-position'   => 'Posición fiscal',
                ],
            ],

            'terms' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Favorito',
            'priority'         => 'Prioridad',
            'vendor-reference' => 'Referencia de proveedor',
            'reference'        => 'Referencia',
            'vendor'           => 'Proveedor',
            'buyer'            => 'Comprador',
            'company'          => 'Empresa',
            'order-deadline'   => 'Fecha límite del pedido',
            'source-document'  => 'Documento de origen',
            'untaxed-amount'   => 'Importe sin impuestos',
            'total-amount'     => 'Importe total',
            'status'           => 'Estado',
            'billing-status'   => 'Estado de facturación',
            'receipt-status'   => 'Estado de recepción',
            'currency'         => 'Moneda',
        ],

        'groups' => [
            'vendor'     => 'Proveedor',
            'buyer'      => 'Comprador',
            'state'      => 'Estado',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'status'           => 'Estado',
            'vendor-reference' => 'Referencia de proveedor',
            'reference'        => 'Referencia',
            'untaxed-amount'   => 'Importe sin impuestos',
            'total-amount'     => 'Importe total',
            'order-deadline'   => 'Fecha límite del pedido',
            'vendor'           => 'Proveedor',
            'buyer'            => 'Comprador',
            'company'          => 'Empresa',
            'payment-term'     => 'Condición de pago',
            'incoterm'         => 'Incoterm',
            'status'           => 'Estado',
            'created-at'       => 'Creado el',
            'updated-at'       => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pedido eliminado',
                        'body'  => 'El pedido se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el pedido',
                        'body'  => 'El pedido no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pedidos eliminados',
                        'body'  => 'Los pedidos se han eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los pedidos',
                        'body'  => 'Los pedidos no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'tax' => 'Impuesto',
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'purchase-order'           => 'Pedido de compra',
                    'vendor'                   => 'Proveedor',
                    'vendor-reference'         => 'Referencia de proveedor',
                    'vendor-reference-tooltip' => 'El número de referencia del pedido de venta o de la oferta proporcionado por el proveedor. Se utiliza para la conciliación al recibir productos, ya que esta referencia suele incluirse en el albarán de entrega del proveedor.',
                    'agreement'                => 'Acuerdo',
                    'currency'                 => 'Moneda',
                    'confirmation-date'        => 'Fecha de confirmación',
                    'order-deadline'           => 'Fecha límite del pedido',
                    'expected-arrival'         => 'Llegada prevista',
                    'confirmed-by-vendor'      => 'Confirmado por el proveedor',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'repeater' => [
                    'products' => [
                        'title'            => 'Productos',
                        'add-product-line' => 'Añadir producto',

                        'entries' => [
                            'product'             => 'Producto',
                            'expected-arrival'    => 'Llegada prevista',
                            'quantity'            => 'Cantidad',
                            'received'            => 'Recibido',
                            'billed'              => 'Facturado',
                            'unit'                => 'Unidad',
                            'packaging-qty'       => 'Cantidad por embalaje',
                            'packaging'           => 'Embalaje',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Descuento (%)',
                            'unit-price'          => 'Precio unitario',
                            'amount'              => 'Importe',
                        ],
                    ],

                    'section' => [
                        'title' => 'Añadir sección',
                    ],

                    'note' => [
                        'title' => 'Añadir nota',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Información adicional',

                'entries' => [
                    'buyer'             => 'Comprador',
                    'company'           => 'Empresa',
                    'source-document'   => 'Documento de origen',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Los términos comerciales internacionales (Incoterms) son un conjunto de términos comerciales estandarizados utilizados en transacciones globales para definir las responsabilidades entre compradores y vendedores.',
                    'incoterm-location' => 'Ubicación del Incoterm',
                    'payment-term'      => 'Condición de pago',
                    'fiscal-position'   => 'Posición fiscal',
                ],
            ],

            'terms' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],
];
