<?php

return [
    'navigation' => [
        'title' => 'Etapas de tarea',
    ],

    'form' => [
        'name'    => 'Nombre',
        'project' => 'Proyecto',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'project'    => 'Proyecto',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'project'    => 'Proyecto',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'project' => 'Proyecto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa de tarea actualizada',
                    'body'  => 'La etapa de tarea se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Etapa de tarea restaurada',
                    'body'  => 'La etapa de tarea se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa de tarea eliminada',
                    'body'  => 'La etapa de tarea se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Etapa de tarea eliminada permanentemente',
                        'body'  => 'La etapa de tarea se ha eliminado permanentemente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar la etapa de tarea',
                        'body'  => 'La etapa de tarea no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Etapas de tarea restauradas',
                    'body'  => 'Las etapas de tarea se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapas de tarea eliminadas',
                    'body'  => 'Las etapas de tarea se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etapas de tarea eliminadas permanentemente',
                    'body'  => 'Las etapas de tarea se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],
];
