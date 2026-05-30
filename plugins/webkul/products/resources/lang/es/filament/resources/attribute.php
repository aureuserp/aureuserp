<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name' => 'Nombre',
                    'type' => 'Tipo',
                ],
            ],

            'options' => [
                'title'  => 'Opciones',

                'fields' => [
                    'name'        => 'Nombre',
                    'color'       => 'Color',
                    'extra-price' => 'Precio extra',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nombre',
            'type'        => 'Tipo',
            'deleted-at'  => 'Eliminado el',
            'created-at'  => 'Creado el',
            'updated-at'  => 'Actualizado el',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'type' => 'Tipo',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atributo restaurado',
                    'body'  => 'El atributo ha sido restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributo eliminado',
                    'body'  => 'El atributo ha sido eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atributo eliminado definitivamente',
                        'body'  => 'El atributo ha sido eliminado permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el atributo',
                        'body'  => 'El atributo no puede eliminarse porque está en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atributos restaurados',
                    'body'  => 'Los atributos han sido restaurados correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atributos eliminados',
                    'body'  => 'Los atributos han sido eliminados correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atributos eliminados definitivamente',
                        'body'  => 'Los atributos han sido eliminados definitivamente de forma correcta.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los atributos',
                        'body'  => 'Los atributos no pueden eliminarse porque están en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name' => 'Nombre',
                    'type' => 'Tipo',
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'creator'    => 'Creado por',
                    'created_at' => 'Creado el',
                    'updated_at' => 'Última actualización',
                ],
            ],
        ],
    ],
];
