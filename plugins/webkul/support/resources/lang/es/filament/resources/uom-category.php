<?php

return [
    'navigation' => [
        'title' => 'Categorías de unidad de medida',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name' => 'Nombre',
                ],
            ],

            'uoms' => [
                'title' => 'Unidades de medida',

                'fields' => [
                    'uoms'     => 'Unidades',
                    'type'     => 'Tipo',
                    'name'     => 'Nombre',
                    'factor'   => 'Factor',
                    'rounding' => 'Precisión de redondeo',
                ],

                'actions' => [
                    'add' => 'Añadir unidad',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'uoms'       => 'Unidades de medida',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoría de unidad de medida actualizada',
                    'body'  => 'La categoría de unidad de medida se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoría de unidad de medida eliminada',
                    'body'  => 'La categoría de unidad de medida se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorías de unidad de medida eliminadas',
                    'body'  => 'Las categorías de unidad de medida se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
