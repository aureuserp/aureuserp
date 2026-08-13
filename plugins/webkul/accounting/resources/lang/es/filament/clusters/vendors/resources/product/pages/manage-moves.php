<?php

return [
    'title' => 'ENT/SAL',

    'table' => [
        'columns' => [
            'date'                 => 'Fecha',
            'reference'            => 'Referencia',
            'product'              => 'Producto',
            'package'              => 'Paquete',
            'lot'                  => 'Números de lote / serie',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'quantity'             => 'Cantidad',
            'state'                => 'Estado',
            'done-by'              => 'Realizado por',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Movimiento eliminado',
                    'body'  => 'El movimiento se ha eliminado correctamente.',
                ],
            ],
        ],
    ],
];
