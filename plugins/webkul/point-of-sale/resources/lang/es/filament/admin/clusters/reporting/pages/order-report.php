<?php

return [
    'navigation' => [
        'title' => 'Pedidos',
    ],

    'title'      => 'Análisis de pedidos',

    'filters' => [
        'heading'       => 'Filtros',
        'all-terminals' => 'Todos los terminales',
        'day'           => 'Por día',
        'week'          => 'Por semana',
        'month'         => 'Por mes',
    ],

    'table' => [
        'heading' => 'Pedidos',
        'total'   => 'Total',
        'empty'   => 'No hay pedidos en este periodo.',

        'columns' => [
            'period'  => 'Periodo',
            'orders'  => 'Pedidos',
            'untaxed' => 'Base imponible',
            'taxes'   => 'Impuestos',
            'total'   => 'Total',
            'margin'  => 'Margen',
        ],
    ],

    'average'    => 'Valor medio del pedido: :amount',
];
