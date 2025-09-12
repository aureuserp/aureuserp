<?php

return [
    'title' => 'Nota de Crédito de Proveedor',

    'navigation' => [
        'title' => 'Reembolsos', // O 'Notas de Crédito de Proveedor'
        'group' => 'Facturas',
    ],

    'global-search' => [
        'number'           => 'Número',
        'customer'         => 'Cliente', // En este contexto, podría ser 'Proveedor' si es una búsqueda de notas de crédito de proveedor
        'invoice-date'     => 'Fecha de Factura', // O 'Fecha de Nota de Crédito'
        'invoice-due-date' => 'Fecha de Vencimiento de Factura', // O 'Fecha de Vencimiento de Nota de Crédito'
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'vendor-credit-note' => 'Nota de Crédito de Proveedor',
                    'vendor'             => 'Proveedor',
                    'bill-date'          => 'Fecha de Factura',
                    'bill-reference'     => 'Referencia de Factura',
                    'accounting-date'    => 'Fecha Contable',
                    'payment-reference'  => 'Referencia de Pago',
                    'recipient-bank'     => 'Banco Receptor',
                    'due-date'           => 'Fecha de Vencimiento',
                    'payment-term'       => 'Condición de Pago',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de Factura', // O 'Líneas de Nota de Crédito'

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
                        'title'  => 'Seguridad',
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
                    'vendor-invoice'    => 'Factura de Proveedor', // En este contexto, 'Nota de Crédito de Proveedor' sería más preciso.
                    'vendor'            => 'Proveedor',
                    'bill-date'         => 'Fecha de Factura',
                    'bill-reference'    => 'Referencia de Factura',
                    'accounting-date'   => 'Fecha Contable',
                    'payment-reference' => 'Referencia de Pago',
                    'recipient-bank'    => 'Banco Receptor',
                    'due-date'          => 'Fecha de Vencimiento',
                    'payment-term'      => 'Condición de Pago',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de Factura', // O 'Líneas de Nota de Crédito'

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
                        'title'   => 'Seguridad',
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
