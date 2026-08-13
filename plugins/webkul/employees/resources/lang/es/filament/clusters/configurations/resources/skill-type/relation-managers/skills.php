<?php

return [
    'form' => [
        'name' => 'Nombre',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'deleted-records' => 'Registros eliminados',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Competencia actualizada',
                    'body'  => 'La competencia se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Competencia restaurada',
                    'body'  => 'La competencia se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Competencia eliminada',
                    'body'  => 'La competencia se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Competencias eliminadas',
                    'body'  => 'Las competencias se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Competencias eliminadas permanentemente',
                    'body'  => 'Las competencias se han eliminado permanentemente correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Competencias restauradas',
                    'body'  => 'Las competencias se han restaurado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nombre',
        ],
    ],
];
