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
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta actualizada',
                    'body'  => 'La etiqueta ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Etiqueta restaurada',
                    'body'  => 'La etiqueta ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada',
                    'body'  => 'La etiqueta ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada permanentemente',
                    'body'  => 'La etiqueta ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Etiquetas restauradas',
                    'body'  => 'Las etiquetas han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas',
                    'body'  => 'Las etiquetas han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas permanentemente',
                    'body'  => 'Las etiquetas han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],
];