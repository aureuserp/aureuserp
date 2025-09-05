<?php

return [
    'navigation' => [
        'title' => 'Órdenes',
    ],

    'tabs' => [
        'my-purchases'    => 'Mis Compras',
        'starred'         => 'Destacadas',
        'purchase-orders' => 'Órdenes de Compra',
        'orders'          => 'Solicitudes de Cotización', // RFQs
        'draft-orders'    => 'Solicitudes de Cotización Borrador',
        'waiting-orders'  => 'Solicitudes de Cotización Pendientes',
        'late-orders'     => 'Solicitudes de Cotización Atrasadas',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'Nueva Orden',

            'notification' => [
                'title' => 'Orden creada',
                'body'  => 'La orden ha sido creada exitosamente.',
            ],
        ],
    ],
];