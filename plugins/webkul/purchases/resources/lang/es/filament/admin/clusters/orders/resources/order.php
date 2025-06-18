<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'vendor'                   => 'Proveedor',
                    'vendor-reference'         => 'Referencia del Proveedor',
                    'vendor-reference-tooltip' => 'El número de referencia de la orden de venta o la oferta proporcionada por el proveedor. Se utiliza para la conciliación al recibir productos, ya que esta referencia suele incluirse en la orden de entrega del proveedor.',
                    'agreement'                => 'Acuerdo',
                    'currency'                 => 'Moneda',
                    'confirmation-date'        => 'Fecha de Confirmación',
                    'order-deadline'           => 'Fecha Límite de la Orden',
                    'expected-arrival'         => 'Llegada Prevista',
                    'confirmed-by-vendor'      => 'Confirmado por el Proveedor',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'repeater' => [
                    'products' => [
                        'title'            => 'Productos',
                        'add-product-line' => 'Añadir Producto',

                        'fields' => [
                            'product'             => 'Producto',
                            'expected-arrival'    => 'Llegada Prevista',
                            'quantity'            => 'Cantidad',
                            'received'            => 'Recibido',
                            'billed'              => 'Facturado',
                            'unit'                => 'Unidad',
                            'packaging-qty'       => 'Cant. Empaque',
                            'packaging'           => 'Empaque',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Descuento (%)',
                            'unit-price'          => 'Precio Unitario',
                            'amount'              => 'Importe',
                        ],
                    ],

                    'section' => [
                        'title' => 'Añadir Sección',

                        'fields' => [
                        ],
                    ],

                    'note' => [
                        'title' => 'Añadir Nota',

                        'fields' => [
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Información Adicional',

                'fields' => [
                    'buyer'             => 'Comprador',
                    'company'           => 'Empresa',
                    'source-document'   => 'Documento de Origen',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Los Términos de Comercio Internacional (Incoterms) son un conjunto de términos comerciales estandarizados utilizados en transacciones globales para definir las responsabilidades entre compradores y vendedores.',
                    'incoterm-location' => 'Ubicación del Incoterm',
                    'payment-term'      => 'Término de Pago',
                    'fiscal-position'   => 'Posición Fiscal',
                ],
            ],

            'terms' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'priority'         => 'Prioridad',
            'vendor-reference' => 'Referencia del Proveedor',
            'reference'        => 'Referencia',
            'vendor'           => 'Proveedor',
            'buyer'            => 'Comprador',
            'company'          => 'Empresa',
            'order-deadline'   => 'Fecha Límite de la Orden',
            'source-document'  => 'Documento de Origen',
            'untaxed-amount'   => 'Importe Sin Impuestos',
            'total-amount'     => 'Importe Total',
            'status'           => 'Estado',
            'billing-status'   => 'Estado de Facturación',
            'currency'         => 'Moneda',
            // 'billing-status' => 'Estado de Facturación', // Duplicado, se mantiene uno.
        ],

        'groups' => [
            'vendor'     => 'Proveedor',
            'buyer'      => 'Comprador',
            'state'      => 'Estado',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'status'           => 'Estado',
            'vendor-reference' => 'Referencia del Proveedor',
            'reference'        => 'Referencia',
            'untaxed-amount'   => 'Importe Sin Impuestos',
            'total-amount'     => 'Importe Total',
            'order-deadline'   => 'Fecha Límite de la Orden',
            'vendor'           => 'Proveedor',
            'buyer'            => 'Comprador',
            'company'          => 'Empresa',
            'payment-term'     => 'Término de Pago',
            'incoterm'         => 'Incoterm',
            // 'status' => 'Estado', // Duplicado, se mantiene uno.
            'created-at'       => 'Creado El',
            'updated-at'       => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Orden eliminada',
                        'body'  => 'La orden ha sido eliminada exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la orden',
                        'body'  => 'La orden no puede ser eliminada porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Órdenes eliminadas',
                        'body'  => 'Las órdenes han sido eliminadas exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las órdenes',
                        'body'  => 'Las órdenes no pueden ser eliminadas porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'vendor'                   => 'Proveedor',
                    'vendor-reference'         => 'Referencia del Proveedor',
                    'vendor-reference-tooltip' => 'El número de referencia de la orden de venta o la oferta proporcionada por el proveedor. Se utiliza para la conciliación al recibir productos, ya que esta referencia suele incluirse en la orden de entrega del proveedor.',
                    'agreement'                => 'Acuerdo',
                    'currency'                 => 'Moneda',
                    'confirmation-date'        => 'Fecha de Confirmación',
                    'order-deadline'           => 'Fecha Límite de la Orden',
                    'expected-arrival'         => 'Llegada Prevista',
                    'confirmed-by-vendor'      => 'Confirmado por el Proveedor',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Productos',

                'repeater' => [
                    'products' => [
                        'title'            => 'Productos',
                        'add-product-line' => 'Añadir Producto',

                        'entries' => [
                            'product'             => 'Producto',
                            'expected-arrival'    => 'Llegada Prevista',
                            'quantity'            => 'Cantidad',
                            'received'            => 'Recibido',
                            'billed'              => 'Facturado',
                            'unit'                => 'Unidad',
                            'packaging-qty'       => 'Cant. Empaque',
                            'packaging'           => 'Empaque',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Descuento (%)',
                            'unit-price'          => 'Precio Unitario',
                            'amount'              => 'Importe',
                        ],
                    ],

                    'section' => [
                        'title' => 'Añadir Sección',
                    ],

                    'note' => [
                        'title' => 'Añadir Nota',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Información Adicional',

                'entries' => [
                    'buyer'             => 'Comprador',
                    'company'           => 'Empresa',
                    'source-document'   => 'Documento de Origen',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Los Términos de Comercio Internacional (Incoterms) son un conjunto de términos comerciales estandarizados utilizados en transacciones globales para definir las responsabilidades entre compradores y vendedores.',
                    'incoterm-location' => 'Ubicación del Incoterm',
                    'payment-term'      => 'Término de Pago',
                    'fiscal-position'   => 'Posición Fiscal',
                ],
            ],

            'terms' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
    ],
];
