<?php

return [
    'navigation' => [
        'title' => 'Planes de Actividad',
        'group' => 'Actividades',
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
            'created-at' => 'Creado En',
            'updated-at' => 'Actualizado En',
        ],

        'filters' => [
            'name'           => 'Nombre',
            'plugin'         => 'Plugin',
            'activity-types' => 'Tipos de Actividad',
            'company'        => 'Empresa',
            'department'     => 'Departamento',
            'is-active'      => 'Estado',
            'updated-at'     => 'Actualizado En',
            'created-at'     => 'Creado En',
        ],

        'groups' => [
            'status'     => 'Estado',
            'name'       => 'Nombre',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado En',
            'updated-at' => 'Actualizado En',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plan de Actividades restaurado',
                    'body'  => 'El plan de actividades ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan de Actividades eliminado',
                    'body'  => 'El plan de actividades ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plan de Actividades eliminado permanentemente',
                    'body'  => 'El plan de actividades ha sido eliminado permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Planes de Actividades restaurados',
                    'body'  => 'Los planes de actividades han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Planes de Actividades eliminados',
                    'body'  => 'Los planes de actividades han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Planes de Actividades eliminados permanentemente',
                    'body'  => 'Los planes de actividades han sido eliminados permanentemente exitosamente.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Plan de Actividades creado',
                    'body'  => 'El plan de actividades ha sido creado exitosamente.',
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
