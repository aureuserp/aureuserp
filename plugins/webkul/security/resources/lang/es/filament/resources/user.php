<?php

return [
    'title' => 'Usuarios',

    'navigation' => [
        'title' => 'Usuarios',
    ],

    'global-search' => [
        'email' => 'Correo electrónico',
    ],

    'form' => [
        'validation' => [
            'cannot-remove-last-admin'   => 'No se puede quitar el rol de administrador del último usuario administrador.',
            'first-user-must-be-admin'   => 'Al primer usuario del sistema se le debe asignar un rol de administrador.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Información general',
                'fields' => [
                    'name'                  => 'Nombre',
                    'email'                 => 'Correo electrónico',
                    'password'              => 'Contraseña',
                    'password-confirmation' => 'Confirmación de contraseña',
                ],
            ],

            'permissions' => [
                'title'  => 'Permisos',
                'fields' => [
                    'roles'                                    => 'Roles',
                    'permissions'                              => 'Permisos',
                    'resource-permission'                      => 'Permiso de recurso',
                    'resource-permission-self-change-disabled' => 'No puede cambiar su propio permiso de recurso. Pida a otro administrador que lo actualice.',
                    'teams'                                    => 'Equipos',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Idioma y estado',
                'fields' => [
                    'language' => 'Idioma preferido',
                    'status'   => 'Estado',
                ],
            ],

            'multi-company' => [
                'title'             => 'Multiempresa',
                'allowed-companies' => 'Empresas permitidas',
                'default-company'   => 'Empresa predeterminada',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Nombre',
            'email'               => 'Correo electrónico',
            'teams'               => 'Equipos',
            'role'                => 'Rol',
            'resource-permission' => 'Permiso de recurso',
            'default-company'     => 'Empresa predeterminada',
            'allowed-company'     => 'Empresa permitida',
            'created-by'          => 'Creado por',
            'created-at'          => 'Creado el',
            'updated-at'          => 'Actualizado el',
        ],

        'filters' => [
            'resource-permission' => 'Permiso de recurso',
            'teams'               => 'Equipos',
            'roles'               => 'Roles',
            'default-company'     => 'Empresa predeterminada',
            'allowed-companies'   => 'Empresas permitidas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Usuario editado',
                    'body'  => 'El usuario se ha editado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Usuario eliminado',
                    'body'  => 'El usuario se ha eliminado correctamente.',
                    'error' => [
                        'title' => 'No se puede eliminar el usuario',
                        'body'  => 'Este es un usuario predeterminado o no puede eliminarse a sí mismo.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Usuario restaurado',
                    'body'  => 'El usuario se ha restaurado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Usuarios restaurados',
                    'body'  => 'Los usuarios se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Usuarios eliminados',
                    'body'  => 'Los usuarios se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Usuarios eliminados permanentemente',
                    'body'  => 'Los usuarios se han eliminado permanentemente correctamente.',
                    'error' => [
                        'title' => 'No se pudo eliminar el usuario',
                        'body'  => 'El usuario no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Usuarios creados',
                    'body'  => 'Los usuarios se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Información general',
                'entries' => [
                    'name'                  => 'Nombre',
                    'email'                 => 'Correo electrónico',
                    'password'              => 'Contraseña',
                    'password-confirmation' => 'Confirmación de contraseña',
                ],
            ],

            'permissions' => [
                'title'   => 'Permisos',
                'entries' => [
                    'roles'               => 'Roles',
                    'permissions'         => 'Permisos',
                    'resource-permission' => 'Permiso de recurso',
                    'teams'               => 'Equipos',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Idioma y estado',
                'entries' => [
                    'language' => 'Idioma preferido',
                    'status'   => 'Estado',
                ],
            ],

            'multi-company' => [
                'title'             => 'Multiempresa',
                'allowed-companies' => 'Empresas permitidas',
                'default-company'   => 'Empresa predeterminada',
            ],
        ],
    ],
];
