<?php

return [
    'form' => [
        'name'       => 'Nombre',
        'short-name' => 'Nombre corto',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'short-name' => 'Nombre corto',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'creator' => 'Creador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tratamiento actualizado',
                    'body'  => 'El tratamiento se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tratamiento eliminado',
                    'body'  => 'El tratamiento se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tratamientos eliminados',
                    'body'  => 'Los tratamientos se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
