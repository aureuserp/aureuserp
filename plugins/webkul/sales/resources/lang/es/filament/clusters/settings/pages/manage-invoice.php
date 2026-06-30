<?php

return [
    'title' => 'Gestionar factura',

    'breadcrumb' => 'Gestionar factura',

    'navigation' => [
        'title' => 'Gestionar factura',
    ],

    'form' => [
        'invoice-policy' => [
            'label'      => 'Política de facturación',
            'label-help' => 'Definir cómo se generan las facturas a partir de los pedidos de venta.',
            'options'    => [
                'order'    => 'Generar factura según las cantidades pedidas',
                'delivery' => 'Generar factura según las cantidades entregadas',
            ],
        ],
    ],
];
