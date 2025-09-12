<?php

return [
    'title' => 'Proveedores',

    'navigation' => [
        'title' => 'Proveedores',
    ],

    'form' => [
        'fields' => [
            'sales-person'       => 'Vendedor',
            'payment-terms'      => 'Condiciones de Pago',
            'payment-method'     => 'Método de Pago',
            'fiscal-position'    => 'Posición Fiscal',
            'purchase'           => 'Compra',
            'fiscal-information' => 'Información Fiscal',
        ],
        'tabs' => [
            'invoicing' => [
                'title'  => 'Facturación',
                'fields' => [
                    'customer-invoices'                  => 'Facturas de Cliente',
                    'invoice-sending-method'             => 'Método de Envío de Facturas',
                    'invoice-edi-format-store'           => 'Formato de Factura Electrónica',
                    'peppol-eas'                         => 'Dirección Peppol',
                    'endpoint'                           => 'Punto Final',
                    'auto-post-bills'                    => 'Publicar Facturas Automáticamente',
                    'automation'                         => 'Automatización',
                    'ignore-abnormal-invoice-amount'     => 'Ignorar Importe Anormal de Factura',
                    'ignore-abnormal-invoice-date'       => 'Ignorar Fecha Anormal de Factura',
                ],
            ],
            'internal-notes' => [
                'title' => 'Notas Internas',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sales-person'       => 'Vendedor',
            'payment-terms'      => 'Condiciones de Pago',
            'payment-method'     => 'Método de Pago',
            'fiscal-position'    => 'Posición Fiscal',
            'purchase'           => 'Compra',
            'fiscal-information' => 'Información Fiscal',
        ],
        'tabs' => [
            'invoicing' => [
                'title'   => 'Facturación',
                'entries' => [
                    'customer-invoices'                  => 'Facturas de Cliente',
                    'invoice-sending-method'             => 'Método de Envío de Facturas',
                    'invoice-edi-format-store'           => 'Formato de Factura Electrónica',
                    'peppol-eas'                         => 'Dirección Peppol',
                    'endpoint'                           => 'Punto Final',
                    'auto-post-bills'                    => 'Publicar Facturas Automáticamente',
                    'automation'                         => 'Automatización',
                    'ignore-abnormal-invoice-amount'     => 'Ignorar Importe Anormal de Factura',
                    'ignore-abnormal-invoice-date'       => 'Ignorar Fecha Anormal de Factura',
                ],
            ],
            'internal-notes' => [
                'title' => 'Notas Internas',
            ],
        ],
    ],
];
