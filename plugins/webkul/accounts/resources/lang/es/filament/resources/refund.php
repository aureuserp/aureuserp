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
                    'vendor-credit-note' => 'Nota de crédito de proveedor',
                    'vendor'             => 'Proveedor',
                    'bill-date'          => 'Fecha de factura',
                    'bill-reference'     => 'Referencia de factura',
                    'accounting-date'    => 'Fecha contable',
                    'payment-reference'  => 'Referencia de pago',
                    'recipient-bank'     => 'Banco del destinatario',
                    'due-date'           => 'Fecha de vencimiento',
                    'payment-term'       => 'Condición de pago',
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
                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación del Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Asegurado',
                        'fields' => [
                            'payment-method' => 'Método de pago',
                            'auto-post'      => 'Contabilización automática',
                            'checked'        => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Información adicional',
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

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'vendor-invoice'    => 'Factura de proveedor',
                    'vendor'            => 'Proveedor',
                    'bill-date'         => 'Fecha de factura',
                    'bill-reference'    => 'Referencia de factura',
                    'accounting-date'   => 'Fecha contable',
                    'payment-reference' => 'Referencia de pago',
                    'recipient-bank'    => 'Banco del destinatario',
                    'due-date'          => 'Fecha de vencimiento',
                    'payment-term'      => 'Condición de pago',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación del Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Asegurado',
                        'entries' => [
                            'payment-method' => 'Método de pago',
                            'auto-post'      => 'Contabilización automática',
                            'checked'        => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Información adicional',
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
