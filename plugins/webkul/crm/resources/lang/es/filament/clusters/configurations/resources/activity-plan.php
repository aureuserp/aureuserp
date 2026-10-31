<?php

return [
    'model-label'        => 'Plan de actividades',
    'plural-model-label' => 'Planes de actividades',

    'navigation' => [
        'title' => 'Planes de actividades',
        'group' => 'Actividades',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información general',
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
            'manager'    => 'Responsable',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'name'           => 'Nombre',
            'plugin'         => 'Plugin',
            'activity-types' => 'Tipos de actividad',
            'company'        => 'Empresa',
            'department'     => 'Departamento',
            'is-active'      => 'Estado',
            'updated-at'     => 'Actualizado el',
            'created-at'     => 'Creado el',
        ],

        'groups' => [
            'status'     => 'Estado',
            'name'       => 'Nombre',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plan de actividades restaurado',
                    'body'  => 'El plan de actividades se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan de actividades eliminado',
                    'body'  => 'El plan de actividades se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plan de actividades eliminado permanentemente',
                    'body'  => 'El plan de actividades se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Plan de actividades creado',
                    'body'  => 'El plan de actividades se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'name'       => 'Nombre',
                    'status'     => 'Estado',
                    'department' => 'Departamento',
                    'manager'    => 'Responsable',
                    'company'    => 'Empresa',
                ],
            ],
        ],
    ],
];
