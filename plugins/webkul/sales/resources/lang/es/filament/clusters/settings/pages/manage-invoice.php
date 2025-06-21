<?php

return [
    'title' => 'Administrar Factura',

    'breadcrumb' => 'Administrar Factura',

    'navigation' => [
        'title' => 'Administrar Factura',
    ],

    'form' => [
        'invoice-policy' => [
            'label'      => 'Política de Facturación',
            'label-help' => 'Define cómo se generan las facturas a partir de las órdenes de venta.',
            'options'    => [
                'order'    => 'Generar factura basada en las cantidades pedidas',
                'delivery' => 'Generar factura basada en las cantidades entregadas',
            ],
        ],
    ],
];
