<?php

return [
    'navigation' => [
        'title' => 'Ventas',
    ],

    'navigation-group' => [
        'title' => 'Panel',
    ],

    'filters-form' => [
        'start-date'     => 'Fecha de Inicio',
        'end-date'       => 'Fecha de Fin',
        'salesperson'    => 'Vendedor',
        'country'        => 'País',
        'product'        => 'Producto',
        'customer'       => 'Cliente',
        'category'       => 'Categoría',
        'salesteam'      => 'Equipo de Ventas',
    ],

    'widgets' => [
        'stats-overview' => [
            'heading'          => 'Resumen de Ventas',
            'quotation'        => 'Cotización',
            'order'            => 'Pedido',
            'draft'            => 'Cotización Borrador',
            'cancel'           => 'Cancelar Cotización',
            'total-revenue'    => 'Ingresos Totales',
            'avg-revenue'      => 'Ingresos Promedio',
            'fully-invoiced'   => 'Totalmente Facturado',
            'archived'         => 'Archivado',
            'no-change'        => 'Sin Cambios',
            'increase'         => 'Aumento',
            'decrease'         => 'Disminución',

            'descriptions' => [
                'quotation'     => 'Cotizaciones enviadas a clientes',
                'order'         => 'Pedidos confirmados por clientes',
                'draft'         => 'Cotizaciones en borrador',
                'cancel'        => 'Cotización cancelada por clientes',
                'total-revenue' => 'Ingresos totales de los pedidos',
                'avg-revenue'   => 'Ingresos promedio de los pedidos',
                'fully-invoiced'=> 'Pedidos totalmente facturados',
                'archived'      => 'Pedidos archivados',
            ],
        ],

        'sales-chart' => [
            'heading'          => 'Gráfico de Ventas',
            'confirmed-orders' => 'Pedidos Confirmados',
            'draft-orders'     => 'Pedidos en Borrador',
            'sent-orders'      => 'Cotizaciones Enviadas',
            'cancelled-orders' => 'Pedidos Cancelados',
        ],

        'revenue-chart' => [
            'heading' => 'Gráfico de Ingresos',
            'label'   => 'Ingresos',
        ],

        'yearly-comparison' => [
            'heading' => 'Comparación Anual de Ventas',
            'label'   => 'Ventas',
        ],

        'top-categories' => [
            'heading' => 'Mejores Categorías',
            'column'  => [
                'category'              => 'Categoría',
                'category_full_name'    => 'Nombre Completo',
                'product_count'         => 'Cantidad de Productos',
            ],
        ],

        'top-customers' => [
            'heading' => 'Mejores Clientes',
            'column'  => [
                'customer'      => 'Cliente',
                'total_orders'  => 'Total de Pedidos',
                'total_revenue' => 'Ingresos Totales',
            ],
        ],

        'top-products' => [
            'heading' => 'Mejores Productos',
            'column'  => [
                'product'       => 'Producto',
                'qty_sold'      => 'Cantidad Vendida',
                'total_revenue' => 'Ingresos Totales',
            ],
        ],

        'top-sales-teams' => [
            'heading' => 'Mejores Equipos de Ventas',
            'column'  => [
                'sales_team'    => 'Equipo de Ventas',
                'total_orders'  => 'Total de Pedidos',
                'total_revenue' => 'Ingresos',
            ],
        ],

        'top-sales-orders' => [
            'heading' => 'Mejores Pedidos de Venta',
            'column'  => [
                'order'         => 'Pedido',
                'customer'      => 'Cliente',
                'order_date'    => 'Fecha del Pedido',
                'total_amount'  => 'Importe Total',
            ],
        ],

        'top-sales-countries' => [
            'heading' => 'Mejores Países de Venta',
            'column'  => [
                'country'        => 'País',
                'total_products' => 'Total de Productos',
                'total_revenue'  => 'Ingresos Totales',
            ],
        ],
    ],
];
