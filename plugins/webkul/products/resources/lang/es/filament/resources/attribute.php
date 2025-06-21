<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name' => 'Nombre',
                    'type' => 'Tipo',
                ],
            ],

            'options' => [
                'title' => 'Opciones',

                'fields' => [
                    'name'        => 'Nombre',
                    'color'       => 'Color',
                    'extra-price' => 'Precio Extra',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'type'       => 'Tipo',
            'deleted-at' => 'Eliminado El',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'type' => 'Tipo',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atributo restaurado',
                    'body'  => 'El atributo ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atributo eliminado permanentemente',
                        'body'  => 'El atributo ha sido eliminado permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el atributo',
                        'body'  => 'El atributo no puede ser eliminado porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atributos restaurados',
                    'body'  => 'Los atributos han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributos eliminados',
                    'body'  => 'Los atributos han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atributos eliminados permanentemente',
                        'body'  => 'Los atributos han sido eliminados permanentemente exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los atributos',
                        'body'  => 'Los atributos no pueden ser eliminados porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información General',

                'entries' => [
                    'name' => 'Nombre',
                    'type' => 'Tipo',
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'creator'    => 'Creado Por',
                    'created_at' => 'Creado El',
                    'updated_at' => 'Última Actualización El',
                ],
            ],
        ],
    ],
];
