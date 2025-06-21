<?php

return [
    'navigation' => [
        'title' => 'Planes de Actividad',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información General',
                'fields' => [
                    'name'       => 'Nombre',
                    'status'     => 'Estado',
                    'department' => 'Departamento',
                    'company'    => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'status'     => 'Estado',
            'department' => 'Departamento',
            'company'    => 'Empresa',
            'manager'    => 'Gerente',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'name'           => 'Nombre',
            'plugin'         => 'Plugin',
            'activity-types' => 'Tipos de Actividad',
            'company'        => 'Empresa',
            'department'     => 'Departamento',
            'is-active'      => 'Estado',
            'updated-at'     => 'Actualizado El',
            'created-at'     => 'Creado El',
        ],

        'groups' => [
            'status'     => 'Estado',
            'name'       => 'Nombre',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plan de Actividad restaurado',
                    'body'  => 'El plan de actividad ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan de Actividad eliminado',
                    'body'  => 'El plan de actividad ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plan de Actividad eliminado permanentemente',
                    'body'  => 'El plan de actividad ha sido eliminado permanentemente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Planes de Actividad restaurados',
                    'body'  => 'Los planes de actividad han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Planes de Actividad eliminados',
                    'body'  => 'Los planes de actividad han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Planes de Actividad eliminados permanentemente',
                    'body'  => 'Los planes de actividad han sido eliminados permanentemente.',
                ],
            ],
        ],

        'activity-plan' => [
            'create' => [
                'notification' => [
                    'title' => 'Plan de Actividad creado',
                    'body'  => 'El plan de actividad ha sido creado exitosamente.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Plan de Actividad creado',
                    'body'  => 'El plan de actividad ha sido creado exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información General',
                'entries' => [
                    'name'       => 'Nombre',
                    'status'     => 'Estado',
                    'department' => 'Departamento',
                    'manager'    => 'Gerente',
                    'company'    => 'Empresa',
                ],
            ],
        ],
    ],
];
