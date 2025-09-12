<?php

return [
    'title' => 'Nota de Crédito',

    'navigation' => [
        'title' => 'Notas de Crédito',
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
                    'customer-invoice' => 'Nota de Crédito de Cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de Factura',
                    'due-date'         => 'Fecha de Vencimiento',
                    'payment-term'     => 'Condición de Pago',
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
                    'invoice' => [
                        'title'  => 'Factura',
                        'fields' => [
                            'customer-reference' => 'Referencia de Cliente',
                            'sales-person'       => 'Vendedor',
                            'payment-reference'  => 'Referencia de Pago',
                            'recipient-bank'     => 'Banco del Receptor',
                            'delivery-date'      => 'Fecha de Entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación Incoterm',
                            'payment-method'    => 'Método de Pago',
                            'auto-post'         => 'Publicación Automática',
                            'checked'           => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Información Adicional',
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
                            'source'   => 'Origen',
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
                    'customer-invoice' => 'Nota de Crédito de Cliente',
                    'customer'         => 'Cliente',
                    'invoice-date'     => 'Fecha de Factura',
                    'due-date'         => 'Fecha de Vencimiento',
                    'payment-term'     => 'Condición de Pago',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Líneas de Factura',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Producto',
                            'quantity'            => 'Cantidad',
                            'unit'                => 'Unidad de Medida',
                            'taxes'               => 'Impuestos',
                            'discount-percentage' => 'Porcentaje de Descuento',
                            'unit-price'          => 'Precio Unitario',
                            'sub-total'           => 'Subtotal',
                            'total'               => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Otra Información',
                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Factura',
                        'entries' => [
                            'customer-reference' => 'Referencia de Cliente',
                            'sales-person'       => 'Vendedor',
                            'payment-reference'  => 'Referencia de Pago',
                            'recipient-bank'     => 'Banco del Receptor',
                            'delivery-date'      => 'Fecha de Entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidad',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Ubicación Incoterm',
                            'payment-method'    => 'Método de Pago',
                            'auto-post'         => 'Publicación Automática',
                            'checked'           => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Información Adicional',
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
                            'source'   => 'Origen',
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
