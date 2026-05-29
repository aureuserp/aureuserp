<?php

return [
    'title' => 'Movimientos',

    'table' => [
        'columns' => [
            'date'                 => 'Fecha',
            'reference'            => 'Referencia',
            'product'              => 'Producto',
            'package'              => 'Paquete',
            'lot'                  => 'Lote / Números de serie',
            'source-location'      => 'Ubicación de origen',
            'destination-location' => 'Ubicación de destino',
            'quantity'             => 'Cantidad',
            'unit'                 => 'Unidad',
            'state'                => 'Estado',
            'done-by'              => 'Realizado por',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Movimiento eliminado',
                    'body'  => 'El movimiento ha sido eliminado exitosamente.',
                ],
            ],
        ],
    ],
];
