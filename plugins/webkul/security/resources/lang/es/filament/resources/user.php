<?php

return [
    'title' => 'Usuarios',

    'navigation' => [
        'title' => 'Usuarios',
        'group' => 'Ajustes',
    ],

    'global-search' => [
        'name'  => 'Nombre',
        'email' => 'Correo Electrónico',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title'  => 'Información General',
                'fields' => [
                    'name'                  => 'Nombre',
                    'email'                 => 'Correo Electrónico',
                    'password'              => 'Contraseña',
                    'password-confirmation' => 'Confirmar Contraseña',
                ],
            ],

            'permissions' => [
                'title'  => 'Permisos',
                'fields' => [
                    'roles'               => 'Roles',
                    'permissions'         => 'Permisos',
                    'resource-permission' => 'Permiso de Recurso',
                    'teams'               => 'Equipos',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Idioma y Estado',
                'fields' => [
                    'language' => 'Idioma Preferido',
                    'status'   => 'Estado',
                ],
            ],

            'multi-company' => [
                'title'             => 'Multi-Empresa',
                'allowed-companies' => 'Empresas Permitidas',
                'default-company'   => 'Empresa por Defecto',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Nombre',
            'email'               => 'Correo Electrónico',
            'teams'               => 'Equipos',
            'role'                => 'Rol',
            'resource-permission' => 'Permiso de Recurso',
            'default-company'     => 'Empresa por Defecto',
            'allowed-company'     => 'Empresa Permitida',
            'created-at'          => 'Creado El',
            'updated-at'          => 'Actualizado El',
        ],

        'filters' => [
            'resource-permission' => 'Permiso de Recurso',
            'teams'               => 'Equipos',
            'roles'               => 'Roles',
            'default-company'     => 'Empresa por Defecto',
            'allowed-companies'   => 'Empresas Permitidas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Usuario editado',
                    'body'  => 'El usuario ha sido editado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Usuario eliminado',
                    'body'  => 'El usuario ha sido eliminado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Usuario restaurado',
                    'body'  => 'El usuario ha sido restaurado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Usuarios restaurados',
                    'body'  => 'Los usuarios han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Usuarios eliminados',
                    'body'  => 'Los usuarios han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Usuarios eliminados permanentemente',
                    'body'  => 'Los usuarios han sido eliminados permanentemente exitosamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Usuarios creados',
                    'body'  => 'Los usuarios han sido creados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Información General',
                'entries' => [
                    'name'                  => 'Nombre',
                    'email'                 => 'Correo Electrónico',
                    'password'              => 'Contraseña',
                    'password-confirmation' => 'Confirmar Contraseña',
                ],
            ],

            'permissions' => [
                'title'   => 'Permisos',
                'entries' => [
                    'roles'               => 'Roles',
                    'permissions'         => 'Permisos',
                    'resource-permission' => 'Permiso de Recurso',
                    'teams'               => 'Equipos',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Idioma y Estado',
                'entries' => [
                    'language' => 'Idioma Preferido',
                    'status'   => 'Estado',
                ],
            ],

            'multi-company' => [
                'title'             => 'Multi-Empresa',
                'allowed-companies' => 'Empresas Permitidas',
                'default-company'   => 'Empresa por Defecto',
            ],
        ],
    ],
];
