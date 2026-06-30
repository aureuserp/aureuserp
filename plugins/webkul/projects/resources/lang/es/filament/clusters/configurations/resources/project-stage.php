<?php

return [
    'navigation' => [
        'title' => 'Etapas de proyecto',
    ],

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
            'name'         => 'Nombre',
            'is-completed' => 'Está completada',
            'project'      => 'Proyecto',
            'created-at'   => 'Creado el',
        ],

        'filters' => [
            'is-completed' => 'Está completada',
            'project'      => 'Proyecto',
            'creator'      => 'Creador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa de proyecto actualizada',
                    'body'  => 'La etapa de proyecto se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Etapa de proyecto restaurada',
                    'body'  => 'La etapa de proyecto se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa de proyecto eliminada',
                    'body'  => 'La etapa de proyecto se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Etapa de proyecto eliminada permanentemente',
                        'body'  => 'La etapa de proyecto se ha eliminado permanentemente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar la etapa de proyecto',
                        'body'  => 'La etapa de proyecto no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Etapas de proyecto restauradas',
                    'body'  => 'Las etapas de proyecto se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapas de proyecto eliminadas',
                    'body'  => 'Las etapas de proyecto se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etapas de proyecto eliminadas permanentemente',
                    'body'  => 'Las etapas de proyecto se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],
];
