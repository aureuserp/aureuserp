<?php

return [
    'label'          => 'Devolver productos',
    'refunded-label' => 'Reembolsado',

    'form' => [
        'fields' => [
            'payment-method' => 'Método de pago del reembolso',
            'lines'          => 'Líneas a reembolsar',
            'selected'       => 'Reembolsar',
            'product'        => 'Producto',
            'refundable'     => 'Reembolsable',
            'quantity'       => 'Cantidad',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Reembolso creado',
            'body'  => 'Se ha creado un pedido de reembolso para las líneas seleccionadas.',
        ],
    ],
];
