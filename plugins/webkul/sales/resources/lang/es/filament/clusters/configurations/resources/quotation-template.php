<?php

return [
    'title' => 'Plantilla de Cotización',

    'navigation' => [
        'title' => 'Plantilla de Cotización',
        'group' => 'Órdenes de Venta',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Productos',
                'fields' => [
                    'products' => 'Productos',
                    'name'     => 'Nombre',
                    'quantity' => 'Cantidad',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Términos y Condiciones',
                'fields' => [
                    'note-placeholder' => 'Escriba sus términos y condiciones para las cotizaciones.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Información General',

                'fields' => [
                    'name'               => 'Nombre',
                    'quotation-validity' => 'Validez de la Cotización',
                    'sale-journal'       => 'Diario de Ventas',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Firma y Pagos',

                'fields' => [
                    'online-signature'     => 'Firma En Línea',
                    'online-payment'       => 'Pago En Línea',
                    'prepayment-percentage' => 'Porcentaje de Pago Anticipado',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'          => 'Creado por',
            'company'             => 'Empresa',
            'name'                => 'Nombre',
            'number-of-days'      => 'Número de días',
            'journal'             => 'Diario de Ventas',
            'signature-required'  => 'Firma Requerida',
            'payment-required'    => 'Pago Requerido',
            'prepayment-percentage' => 'Porcentaje de Pago Anticipado',
        ],
        'groups' => [
            'company' => 'Empresa',
            'name'    => 'Nombre',
            'journal' => 'Diario',
        ],
        'filters' => [
            'created-by' => 'Creado Por',
            'company'    => 'Empresa',
            'name'       => 'Nombre',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plantilla de cotización eliminada',
                    'body'  => 'La plantilla de cotización ha sido eliminada exitosamente.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plantilla de cotización eliminada',
                    'body'  => 'La plantilla de cotización ha sido eliminada exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Productos',
            ],
            'terms-and-conditions' => [
                'title' => 'Términos y Condiciones',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Información General',
            ],
            'signature_and_payment' => [
                'title' => 'Firma y Pago',
            ],
        ],
        'entries' => [
            'product'             => 'Producto',
            'description'         => 'Descripción',
            'quantity'            => 'Cantidad',
            'unit-price'          => 'Precio Unitario',
            'section-name'        => 'Nombre de la Sección',
            'note-title'          => 'Título de la Nota',
            'name'                => 'Nombre de la Plantilla',
            'quotation-validity'  => 'Validez de la Cotización',
            'sale-journal'        => 'Diario de Ventas',
            'online-signature'    => 'Firma En Línea',
            'online-payment'      => 'Pago En Línea',
            'prepayment-percentage' => 'Porcentaje de Pago Anticipado',
        ],
    ],
];
