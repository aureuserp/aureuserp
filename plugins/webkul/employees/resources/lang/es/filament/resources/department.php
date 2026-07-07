<?php

return [
    'title' => 'Departamentos',

    'navigation' => [
        'title' => 'Departamentos',
    ],

    'global-search' => [
        'department-manager' => 'Responsable',
        'company'            => 'Empresa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'fields' => [
                    'name'                => 'Nombre',
                    'manager'             => 'Responsable',
                    'parent-department'   => 'Departamento superior',
                    'manager-placeholder' => 'Seleccionar responsable',
                    'company'             => 'Empresa',
                    'company-placeholder' => 'Seleccionar empresa',
                    'color'               => 'Color',
                ],
            ],

            'additional' => [
                'title'       => 'Información adicional',
                'description' => 'Información adicional sobre este departamento.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'manager-name' => 'Responsable',
            'company-name' => 'Empresa',
        ],

        'groups' => [
            'name'       => 'Nombre',
            'manager'    => 'Responsable',
            'company'    => 'Empresa',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'name'         => 'Nombre',
            'manager-name' => 'Responsable',
            'company-name' => 'Empresa',
            'updated-at'   => 'Actualizado el',
            'created-at'   => 'Creado el',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departamento restaurado',
                    'body'  => 'El departamento se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departamento eliminado',
                    'body'  => 'El departamento se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departamento eliminado permanentemente',
                    'body'  => 'El departamento se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departamentos restaurados',
                    'body'  => 'Los departamentos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departamentos eliminados',
                    'body'  => 'Los departamentos se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departamentos eliminados permanentemente',
                    'body'  => 'Los departamentos se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'            => 'Nombre',
                    'manager'         => 'Responsable',
                    'company'         => 'Empresa',
                    'color'           => 'Color',
                    'hierarchy-title' => 'Organización del departamento',
                ],
            ],
        ],
    ],
];
