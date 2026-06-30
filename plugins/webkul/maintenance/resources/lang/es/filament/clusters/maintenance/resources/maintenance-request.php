<?php

return [
    'navigation' => [
        'group' => 'Mantenimiento',
        'title' => 'Solicitudes de mantenimiento',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Solicitud',
                'fields' => [
                    'name'                      => 'Solicitud',
                    'name-placeholder'          => 'p. ej. La pantalla no funciona',
                    'equipment'                 => 'Equipo',
                    'category'                  => 'Categoría',
                    'requested-at'              => 'Fecha de solicitud',
                    'requested-at-hint-tooltip' => 'La fecha en que se reportó la solicitud de mantenimiento.',
                    'maintenance-type'          => 'Tipo de mantenimiento',
                    'recurrent'                 => 'Recurrente',
                    'repeat-every'              => 'Repetir cada',
                    'maintenance-type-options'  => [
                        'corrective' => 'Correctivo',
                        'preventive' => 'Preventivo',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Notas',
                        'fields' => [
                            'description'             => 'Notas internas',
                            'description-placeholder' => 'Notas internas',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instrucciones',
                        'fields' => [
                            'instruction-type'         => 'Tipo de instrucción',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Texto',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Descripción',
                            'instruction-text-placeholder' => 'Descripción',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'team'                      => 'Equipo',
                    'responsible'               => 'Responsable',
                    'scheduled-at'              => 'Fecha programada',
                    'scheduled-at-hint-tooltip' => 'La fecha y hora en que está previsto que comience este trabajo de mantenimiento.',
                    'duration'                  => 'Duración',
                    'duration-hint-tooltip'     => 'Duración esperada del mantenimiento.',
                    'duration-suffix'           => 'horas',
                    'priority'                  => 'Prioridad',
                    'company'                   => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Asuntos',
            'creator'    => 'Creado por el usuario',
            'technician' => 'Técnico',
            'category'   => 'Categoría',
            'stage'      => 'Etapa',
            'company'    => 'Empresa',
        ],

        'groups' => [
            'stage'       => 'Etapa',
            'assigned-to' => 'Asignado a',
            'category'    => 'Categoría',
            'created-by'  => 'Creado por',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Solicitud de mantenimiento restaurada',
                    'body'  => 'La solicitud de mantenimiento se ha restaurado correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Solicitud de mantenimiento archivada',
                    'body'  => 'La solicitud de mantenimiento se ha archivado correctamente.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Solicitud de mantenimiento eliminada',
                        'body'  => 'La solicitud de mantenimiento se ha eliminado de forma permanente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar la solicitud de mantenimiento',
                        'body'  => 'Esta solicitud de mantenimiento está referenciada por otro registro.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Solicitudes de mantenimiento restauradas',
                    'body'  => 'Las solicitudes de mantenimiento seleccionadas se han restaurado correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Solicitudes de mantenimiento archivadas',
                    'body'  => 'Las solicitudes de mantenimiento seleccionadas se han archivado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Solicitud',
                'entries' => [
                    'name'                     => 'Solicitud',
                    'equipment'                => 'Equipo',
                    'category'                 => 'Categoría',
                    'requested-at'             => 'Fecha de solicitud',
                    'maintenance-type'         => 'Tipo de mantenimiento',
                    'instruction-type'         => 'Tipo de instrucción',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Notas internas',
                    'instruction-text'         => 'Descripción',
                ],
            ],

            'settings' => [
                'title'   => 'Configuración',
                'entries' => [
                    'team'            => 'Equipo',
                    'responsible'     => 'Responsable',
                    'scheduled-at'    => 'Fecha programada',
                    'duration'        => 'Duración',
                    'duration-suffix' => 'horas',
                    'priority'        => 'Prioridad',
                    'company'         => 'Empresa',
                ],
            ],
        ],
    ],
];
