<?php

return [
    'stats' => [
        'open-sessions' => [
            'label'       => 'Sesiones abiertas',
            'description' => 'Sesiones todavía en el mostrador',
        ],

        'orders' => [
            'label'       => 'Pedidos',
            'description' => 'Pedidos liquidados en el período seleccionado',
        ],

        'revenue' => [
            'label'       => 'Ingresos',
            'description' => 'Total de pedidos liquidados en el período seleccionado',
        ],

        'failed-operations' => [
            'label'       => 'Operaciones fallidas',
            'description' => 'Pedidos cuyo movimiento de stock necesita atención',
        ],
    ],
];
