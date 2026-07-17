<?php

return [
    'navigation' => [
        'title' => 'Etiquetas',
        'group' => 'Blog',
    ],

    'form' => [
        'name'  => 'Nombre',
        'color' => 'Color',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'color'      => 'Color',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta actualizada',
                    'body'  => 'La etiqueta se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Etiqueta restaurada',
                    'body'  => 'La etiqueta se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada',
                    'body'  => 'La etiqueta se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada definitivamente',
                    'body'  => 'La etiqueta se ha eliminado definitivamente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Etiquetas restauradas',
                    'body'  => 'Las etiquetas se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas',
                    'body'  => 'Las etiquetas se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas definitivamente',
                    'body'  => 'Las etiquetas se han eliminado definitivamente correctamente.',
                ],
            ],
        ],
    ],
];
