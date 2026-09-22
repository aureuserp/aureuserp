<?php

return [
    'unknown'      => 'Desconocido',
    'other'        => 'Otros',
    'share'        => 'Participación',
    'status'       => 'Estado',
    'stats'        => [
        'heading'              => 'Métricas Clave',
        'draft-rfqs'           => 'Solicitudes Borrador',
        'sent-rfqs'            => 'Solicitudes Enviadas',
        'confirmed-orders'     => 'Pedidos Confirmados',
        'total-purchase-value' => 'Valor Total de Compra',
        'increase'             => ':percent% de aumento',
        'decrease'             => ':percent% de disminución',
        'no-change'            => 'Sin cambios',
    ],

    'trend'        => [
        'heading' => 'Tendencia de Pedidos de Compra por Estado',
        'states'  => [
            'draft'    => 'Borrador',
            'sent'     => 'Enviado',
            'purchase' => 'Compra',
            'done'     => 'Completado',
            'canceled' => 'Cancelado',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Participación de Gasto por Proveedor',
        'total-purchased' => 'Total Comprado',
    ],

    'top-orders'   => [
        'heading' => 'Pedidos Principales',
        'columns' => [
            'reference'  => 'Número de Pedido',
            'vendor'     => 'Proveedor',
            'ordered-at' => 'Fecha de Pedido',
            'amount'     => 'Importe Total',
        ],
    ],

    'top-products' => [
        'heading' => 'Productos Más Comprados',
        'columns' => [
            'name'     => 'Producto',
            'quantity' => 'Cantidad Total',
            'value'    => 'Valor Total',
        ],
    ],
];
