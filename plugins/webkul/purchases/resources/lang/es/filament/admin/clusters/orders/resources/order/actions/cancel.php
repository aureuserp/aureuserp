<?php

return [
    'label' => 'Cancelar',

    'action' => [
        'notification' => [
            'warning' => [
                'receipts' => [
                    'title' => 'No se puede cancelar el pedido',
                    'body'  => 'El pedido no se puede cancelar porque tiene recepciones que ya están realizadas.',
                ],

                'bills' => [
                    'title' => 'No se puede cancelar el pedido',
                    'body'  => 'El pedido no se puede cancelar. Primero debe cancelar las facturas de proveedor relacionadas.',
                ],
            ],

            'success' => [
                'title' => 'Pedido cancelado',
                'body'  => 'El pedido se ha cancelado correctamente.',
            ],
        ],
    ],
];
