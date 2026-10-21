<?php

return [

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'order'      => 'Pedido del punto de venta',
                    'ordered-at' => 'Fecha',
                    'customer'   => 'Cliente',
                    'cashier'    => 'Cajero',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title'   => 'Productos',
                'columns' => [
                    'product'            => 'Producto',
                    'lot'                => 'Número de lote/serie',
                    'quantity'           => 'Cantidad',
                    'uom'                => 'UdM',
                    'unit-price'         => 'Precio unitario',
                    'discount'           => '% dto.',
                    'taxes'              => 'Impuestos',
                    'tax-excluded'       => 'Base imponible',
                    'tax-included'       => 'Impuestos incl.',
                    'full-product-name'  => 'Nombre completo del producto',
                    'customer-note'      => 'Nota del cliente',
                    'total-cost'         => 'Coste total',
                    'margin'             => 'Margen',
                    'margin-percent'     => 'Margen (%)',
                    'refunded-quantity'  => 'Cantidad reembolsada',
                ],

                'actions' => [
                    'open-product' => 'Abrir producto',
                ],

                'summary' => [
                    'untaxed'  => 'Base imponible',
                    'taxes'    => 'Impuestos',
                    'rounding' => 'Redondeo',
                    'total'    => 'Total',
                    'paid'     => 'Pagado',
                    'change'   => 'Cambio',
                ],
            ],

            'payments' => [
                'title'  => 'Pagos',
                'add'    => 'Añadir una línea',
                'fields' => [
                    'paid-at'         => 'Fecha',
                    'method'          => 'Método de pago',
                    'amount'          => 'Importe',
                    'card-type'       => 'Número de tarjeta (últimos 4 dígitos)',
                    'card-brand'      => 'Marca de la tarjeta',
                    'cardholder-name' => 'Nombre del titular',
                ],
            ],

            'extra-info' => [
                'title'  => 'Información adicional',
                'fields' => [
                    'receipt-number'  => 'Número de recibo',
                    'tracking-number' => 'Número de seguimiento',
                    'email'           => 'Correo electrónico',
                    'mobile'          => 'Móvil',
                ],
            ],

            'notes' => [
                'title' => 'Notas generales',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'order'         => 'Pedido del punto de venta',
                    'customer'      => 'Cliente',
                    'session'       => 'Sesión',
                    'ordered-at'    => 'Pedido el',
                    'point-of-sale' => 'Punto de venta',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title'   => 'Línea de pedido',
                'entries' => [
                    'product'    => 'Producto',
                    'quantity'   => 'Cantidad',
                    'unit-price' => 'Precio unitario',
                    'taxes'      => 'Impuestos',
                    'discount'   => 'Descuento (%)',
                    'amount'     => 'Importe',
                ],
                'totals' => [
                    'untaxed' => 'Base imponible',
                    'taxes'   => 'Impuestos',
                    'total'   => 'Importe total',
                    'margin'  => 'Margen',
                ],
            ],

            'payments' => [
                'title'   => 'Pagos',
                'entries' => [
                    'method'  => 'Método de pago',
                    'amount'  => 'Importe',
                    'paid-at' => 'Pagado el',
                ],
            ],

            'extra-info' => [
                'title'   => 'Información adicional',
                'entries' => [
                    'receipt-number'  => 'Número de recibo',
                    'tracking-number' => 'Número de seguimiento',
                    'email'           => 'Correo electrónico',
                    'mobile'          => 'Móvil',
                ],
            ],

            'other-information' => [
                'title'   => 'Otra información',
                'entries' => [
                    'reference'      => 'Referencia',
                    'receipt-number' => 'Número de recibo',
                    'operation'      => 'Operación',
                    'cashier'        => 'Cajero',
                    'paid'           => 'Pagado',
                    'change'         => 'Cambio',
                ],
            ],
        ],
    ],
    'navigation' => [
        'title' => 'Pedidos',
        'group' => 'Punto de venta',
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Ref. del pedido',
            'reference'            => 'Número de recibo',
            'session'              => 'Sesión',
            'config'               => 'Punto de venta',
            'partner'              => 'Cliente',
            'ordered-at'           => 'Fecha',
            'amount-total'         => 'Total',
            'amount-paid'          => 'Pagado',
            'user'                 => 'Cajero',
            'has-failed-operation' => 'Operación fallida',
            'is-invoiced'          => 'Facturado',
            'is-edited'            => 'Editado',
            'tracking-number'      => 'Número de seguimiento',
            'state'                => 'Estado',
        ],

        'groups' => [
            'session'    => 'Sesión',
            'config'     => 'Punto de venta',
            'state'      => 'Estado',
            'ordered-at' => 'Pedido el',
        ],

        'filters' => [
            'state'   => 'Estado',
            'session' => 'Sesión',
            'config'  => 'Punto de venta',
        ],
    ],
];
