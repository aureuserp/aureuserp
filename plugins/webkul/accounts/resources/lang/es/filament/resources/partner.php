<?php

return [
    'title' => 'Contactos',

    'navigation' => [
        'title' => 'Contactos',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Ventas',

                        'fields' => [
                            'sales-person'   => 'Vendedor',
                            'payment-terms'  => 'Condiciones de pago',
                            'payment-method' => 'Método de pago',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Compra',

                        'fields' => [
                            'payment-terms'  => 'Condiciones de pago',
                            'payment-method' => 'Método de pago',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Información fiscal',

                        'fields' => [
                            'fiscal-position'    => 'Posición fiscal',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Facturación',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Facturas de cliente',

                        'fields' => [
                            'invoice-sending-method'   => 'Método de envío de facturas',
                            'invoice-edi-format-store' => 'Formato de factura electrónica',
                            'peppol-eas'               => 'Dirección Peppol',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Asientos contables',

                        'fields' => [
                            'account-receivable' => 'Cuenta por cobrar',
                            'account-payable'    => 'Cuenta por pagar',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automatización',

                        'fields' => [
                            'auto-post-bills'                => 'Contabilizar facturas de proveedor automáticamente',
                            'ignore-abnormal-invoice-amount' => 'Ignorar importe de factura anómalo',
                            'ignore-abnormal-invoice-date'   => 'Ignorar fecha de factura anómala',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Notas internas',
            ],
        ],
    ],

    'infolist' => [

        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Ventas',

                        'entries' => [
                            'sales-person'   => 'Vendedor',
                            'payment-terms'  => 'Condiciones de pago',
                            'payment-method' => 'Método de pago',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Compra',

                        'entries' => [
                            'payment-terms'  => 'Condiciones de pago',
                            'payment-method' => 'Método de pago',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Información fiscal',

                        'entries' => [
                            'fiscal-position'    => 'Posición fiscal',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Facturación',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Facturas de cliente',

                        'entries' => [
                            'invoice-sending-method'   => 'Método de envío de facturas',
                            'invoice-edi-format-store' => 'Formato de factura electrónica',
                            'peppol-eas'               => 'Dirección Peppol',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Asientos contables',

                        'entries' => [
                            'account-receivable' => 'Cuenta por cobrar',
                            'account-payable'    => 'Cuenta por pagar',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automatización',

                        'entries' => [
                            'auto-post-bills'                => 'Contabilizar facturas de proveedor automáticamente',
                            'ignore-abnormal-invoice-amount' => 'Ignorar importe de factura anómalo',
                            'ignore-abnormal-invoice-date'   => 'Ignorar fecha de factura anómala',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Notas internas',
            ],
        ],
    ],
];
