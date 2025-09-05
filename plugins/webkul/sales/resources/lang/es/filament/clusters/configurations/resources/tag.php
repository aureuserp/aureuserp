<?php

return [
    'title' => 'Etiqueta',

    'navigation' => [
        'title' => 'Etiquetas',
        'group' => 'Órdenes de Venta',
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
                    'title' => 'Etiqueta de Producto actualizada',
                    'body'  => 'La etiqueta del producto ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de Producto eliminada',
                    'body'  => 'La etiqueta del producto ha sido eliminada exitosamente.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de Producto eliminada',
                    'body'  => 'La etiqueta del producto ha sido eliminada exitosamente.',
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
