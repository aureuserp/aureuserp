<?php

return [
    'title' => 'Etiqueta',

    'navigation' => [
        'title' => 'Etiqueta',
        'group' => 'Pedidos de venta',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nombre',
            'color' => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Creado por',
            'name'       => 'Nombre',
            'color'      => 'Color',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta de producto actualizada',
                    'body'  => 'La etiqueta de producto se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de producto eliminada',
                    'body'  => 'La etiqueta de producto se ha eliminado correctamente.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de producto eliminada',
                    'body'  => 'La etiqueta de producto se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Nombre',
            'color' => 'Color',
        ],
    ],
];
