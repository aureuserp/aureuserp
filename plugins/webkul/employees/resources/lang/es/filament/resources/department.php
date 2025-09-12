<?php

return [
    'title' => 'Departamentos',

    'navigation' => [
        'title' => 'Departamentos',
        'group' => 'Empleados',
    ],

    'global-search' => [
        'name'             => 'Departamento',
        'department-manager' => 'Gerente',
        'company'          => 'Empresa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Información General',

                'fields' => [
                    'name'                => 'Nombre',
                    'manager'             => 'Gerente',
                    'parent-department'   => 'Departamento Superior',
                    'manager-placeholder' => 'Seleccionar Gerente',
                    'company'             => 'Empresa',
                    'company-placeholder' => 'Seleccionar Empresa',
                    'color'               => 'Color',
                ],
            ],

            'additional' => [
                'title'       => 'Información Adicional',
                'description' => 'Información adicional sobre este departamento.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'manager-name' => 'Gerente',
            'company-name' => 'Empresa',
        ],

        'groups' => [
            'name'       => 'Nombre',
            'manager'    => 'Gerente',
            'company'    => 'Empresa',
            'updated-at' => 'Actualizado El',
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'name'         => 'Nombre',
            'manager-name' => 'Gerente',
            'company-name' => 'Empresa',
            'updated-at'   => 'Actualizado El',
            'created-at'   => 'Creado El',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departamento restaurado',
                    'body'  => 'El departamento ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departamento eliminado',
                    'body'  => 'El departamento ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departamento eliminado permanentemente',
                    'body'  => 'El departamento ha sido eliminado permanentemente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departamentos restaurados',
                    'body'  => 'Los departamentos han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departamentos eliminados',
                    'body'  => 'Los departamentos han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departamentos eliminados permanentemente',
                    'body'  => 'Los departamentos han sido eliminados permanentemente.',
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
                    'manager'         => 'Gerente',
                    'company'         => 'Empresa',
                    'color'           => 'Color',
                    'hierarchy-title' => 'Organización del Departamento',
                ],
            ],
        ],
    ],
];
