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
                    'vendor-bill'       => 'Factura de Proveedor',
                    'vendor'            => 'Proveedor',
                    'bill-date'         => 'Fecha de Factura',
                    'bill-reference'    => 'Referencia de Factura',
                    'accounting-date'   => 'Fecha Contable',
                    'payment-reference' => 'Referencia de Pago',
                    'recipient-bank'    => 'Banco del Receptor',
                    'due-date'          => 'Fecha de Vencimiento',
                    'payment-term'      => 'Condición de Pago',
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
                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Asegurado',
                        'fields' => [
                            'payment-method' => 'Método de Pago',
                            'auto-post'      => 'Publicación Automática',
                            'checked'        => 'Verificado',
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
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'vendor-invoice'    => 'Factura de Proveedor',
                    'vendor'            => 'Proveedor',
                    'bill-date'         => 'Fecha de Factura',
                    'bill-reference'    => 'Referencia de Factura',
                    'accounting-date'   => 'Fecha Contable',
                    'payment-reference' => 'Referencia de Pago',
                    'recipient-bank'    => 'Banco del Receptor',
                    'due-date'          => 'Fecha de Vencimiento',
                    'payment-term'      => 'Condición de Pago',
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

                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Contabilidad',

                        'entries' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Asegurado',
                        'entries' => [
                            'payment-method' => 'Método de Pago',
                            'auto-post'      => 'Publicación Automática',
                            'checked'        => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Información Adicional',
                        'entries' => [
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
    ],
];
