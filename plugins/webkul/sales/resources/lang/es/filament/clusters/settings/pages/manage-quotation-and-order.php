<?php

return [
    'title' => 'Administrar Cotización y Pedido',

    'breadcrumb' => 'Administrar Cotización y Pedido',

    'navigation' => [
        'title' => 'Administrar Cotización y Pedido',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'       => 'días',
            'validity'              => 'Validez Predeterminada de la Cotización',
            'validity-help'         => 'El número predeterminado de días durante los cuales una cotización es válida.',
            'lock-confirm-sales'    => 'Bloquear Confirmación de Ventas',
            'lock-confirm-sales-help' => 'Si está habilitado, la orden de venta se bloqueará después de la confirmación.',
        ],
    ],
];
