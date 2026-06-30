<?php

return [
    'form' => [
        'name'      => 'Nombre',
        'full-name' => 'Nombre completo',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'full-name'  => 'Nombre completo',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sector actualizado',
                    'body'  => 'El sector se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Sector restaurado',
                    'body'  => 'El sector se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sector eliminado',
                    'body'  => 'El sector se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sector eliminado permanentemente',
                    'body'  => 'El sector se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sectores restaurados',
                    'body'  => 'Los sectores se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sectores eliminados',
                    'body'  => 'Los sectores se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sectores eliminados permanentemente',
                    'body'  => 'Los sectores se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],
];
