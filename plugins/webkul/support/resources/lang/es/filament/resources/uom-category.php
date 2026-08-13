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
                    'name'     => 'Unidad de medida',
                    'ratio'    => 'Ratio',
                    'rounding' => 'Precisión de redondeo',
                ],

                'validations' => [
                    'missing-reference'          => 'Esta categoría debe tener una unidad de medida de referencia.',
                    'multiple-references'        => 'Esta categoría solo debe tener una unidad de medida de referencia.',
                    'ratio-greater-than-zero'    => 'El ratio de conversión de una unidad de medida no puede ser cero.',
                    'rounding-greater-than-zero' => 'La precisión de redondeo debe ser estrictamente positiva.',
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
