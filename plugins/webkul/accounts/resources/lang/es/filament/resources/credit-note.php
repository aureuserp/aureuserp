<?php

return [
    'title' => 'Factura',

    'navigation' => [
        'title' => 'Facturas',
        'group' => 'Facturas',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'customer-invoice' => 'Nota de crédito de cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de factura',
                    'due-date'         => 'Fecha de vencimiento',
                    'payment-term'     => 'Condición de pago',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación del Incoterm',
                            'payment-method'    => 'Método de pago',
                            'auto-post'         => 'Contabilización automática',
                            'checked'           => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Información adicional',
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
                            'source'   => 'Fuente',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Términos y condiciones',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'customer-invoice' => 'Nota de crédito de cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de factura',
                    'due-date'         => 'Fecha de vencimiento',
                    'payment-term'     => 'Condición de pago',
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

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación del Incoterm',
                            'payment-method'    => 'Método de pago',
                            'auto-post'         => 'Contabilización automática',
                            'checked'           => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Información adicional',
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
                            'source'   => 'Fuente',
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
