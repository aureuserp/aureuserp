<?php

return [
    'title' => 'Pedidos',

    'navigation' => [
        'label' => 'Pedidos',
    ],

    'walk-in' => 'Cliente ocasional',

    'search' => 'Buscar por pedido, recibo o cliente',

    'search-restaurant' => 'Buscar por pedido, recibo, cliente o mesa',

    'select-order' => 'Selecciona un pedido para ver sus líneas.',

    'line-discount' => '−:discount% de descuento',

    'taxes' => 'Impuestos',

    'total' => 'Total',

    'status' => [
        'active' => 'Todos los pedidos activos',
    ],

    'columns' => [
        'date'     => 'Fecha',
        'receipt'  => 'Número de recibo',
        'order'    => 'Número de pedido',
        'customer' => 'Cliente',
        'cashier'  => 'Cajero',
        'tracking' => 'Número de seguimiento',
        'table'    => 'Mesa',
        'total'    => 'Total',
        'status'   => 'Estado',
    ],

    'refund' => [
        'prompt'    => 'Selecciona el/los producto(s) a reembolsar e indica la cantidad',
        'refunded'  => 'Reembolsado:',
        'to-refund' => 'A reembolsar:',
        'qty'       => 'Cant.',
        'price'     => 'Precio',
        'backspace' => 'Retroceso',

        'max-exceeded' => [
            'title' => 'Máximo superado',
            'body'  => 'La cantidad a reembolsar es mayor que la cantidad pedida. Se solicitan :requested y solo se pueden reembolsar :max.',
        ],
    ],

    'actions' => [
        'print'    => 'Imprimir recibo',
        'back'     => 'Atrás',
        'details'  => 'Detalles',
        'refund'   => 'Reembolso',
        'previous' => 'Página anterior',
        'next'     => 'Página siguiente',

        'refund-notification' => [
            'title' => 'Reembolso creado',
            'body'  => 'El reembolso :order está listo para liquidarse.',
        ],

        'cancel' => [
            'label'       => 'Cancelar',
            'heading'     => '¿Cancelar este pedido?',
            'description' => 'El pedido se cierra sin pago y ya no impide cerrar la caja. Esta acción no se puede deshacer.',

            'notification' => [
                'title' => 'Pedido cancelado',
                'body'  => 'Se canceló :order.',
            ],
        ],

        'invoice' => [
            'label'   => 'Factura',
            'heading' => '¿Crear una factura para este pedido?',

            'notification' => [
                'title' => 'Factura creada',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'Aún no hay pedidos',
        'description' => 'Los pedidos registrados durante la sesión abierta aparecerán aquí.',
    ],
];
