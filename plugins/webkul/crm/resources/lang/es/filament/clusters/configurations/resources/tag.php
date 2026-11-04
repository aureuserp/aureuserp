<?php

return [
    'model-label'        => 'Etiqueta',
    'plural-model-label' => 'Etiquetas',

    'navigation' => [
        'title' => 'Etiquetas',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nombre de la etiqueta',
            'color' => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre de la etiqueta',
            'color'      => 'Color',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta actualizada',
                    'body'  => 'La etiqueta se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada',
                    'body'  => 'La etiqueta se ha eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Nombre de la etiqueta',
            'color' => 'Color',
        ],
    ],
];
