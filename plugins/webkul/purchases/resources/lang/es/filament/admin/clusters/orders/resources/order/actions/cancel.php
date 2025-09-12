<?php

return [
    'label' => 'Cancelar',

    'action' => [
        'notification' => [
            'warning' => [
                'receipts' => [
                    'title' => 'No se puede cancelar la orden',
                    'body'  => 'La orden no se puede cancelar ya que tiene recibos ya realizados.',
                ],

                'bills' => [
                    'title' => 'No se puede cancelar la orden',
                    'body'  => 'La orden no se puede cancelar. Primero debe cancelar las facturas de proveedor relacionadas.',
                ],
            ],

            'success' => [
                'title' => 'Orden cancelada',
                'body'  => 'La orden ha sido cancelada exitosamente.',
            ],
        ],
    ],
];
