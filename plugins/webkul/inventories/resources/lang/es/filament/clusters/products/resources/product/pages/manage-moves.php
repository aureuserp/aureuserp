<?php

return [
    'title' => 'ENTRADA/SALIDA',

    'tabs' => [
        'todo'     => 'Por Hacer',
        'done'     => 'Hecho',
        'incoming' => 'Entrante',
        'outgoing' => 'Saliente',
        'internal' => 'Interno',
    ],

    'table' => [
        'columns' => [
            'date'                  => 'Fecha',
            'reference'             => 'Referencia',
            'product'               => 'Producto',
            'package'               => 'Paquete',
            'lot'                   => 'Lote / Números de Serie',
            'source-location'       => 'Ubicación de Origen',
            'destination-location'  => 'Ubicación de Destino',
            'quantity'              => 'Cantidad',
            'state'                 => 'Estado',
            'done-by'               => 'Realizado Por',
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
