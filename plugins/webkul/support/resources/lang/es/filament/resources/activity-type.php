<?php

return [
    'title' => 'Departamentos',

    'navigation' => [
        'title' => 'Departamentos',
        'group' => 'Empleados',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Información general',

                'fields' => [
                    'name'                => 'Tipo de actividad',
                    'name-tooltip'        => 'Introduzca el nombre oficial del tipo de actividad',
                    'action'              => 'Acción',
                    'default-user'        => 'Usuario predeterminado',
                    'summary'             => 'Resumen',
                    'note'                => 'Nota',
                ],
            ],

            'delay-information' => [
                'title' => 'Información de retraso',

                'fields' => [
                    'delay-count'            => 'Cantidad de retraso',
                    'delay-unit'             => 'Unidad de retraso',
                    'delay-form'             => 'Origen del retraso',
                    'delay-form-helper-text' => 'Fuente del cálculo del retraso',
                ],
            ],

            'advanced-information' => [
                'title' => 'Información avanzada',

                'fields' => [
                    'icon'                => 'Icono',
                    'decoration-type'     => 'Tipo de decoración',
                    'chaining-type'       => 'Tipo de encadenamiento',
                    'suggest'             => 'Sugerir',
                    'trigger'             => 'Activar',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Estado y configuración',

                'fields' => [
                    'status'               => 'Estado',
                    'keep-done-activities' => 'Mantener actividades completadas',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Tipo de actividad',
            'summary'    => 'Resumen',
            'planned-in' => 'Planificado en',
            'type'       => 'Tipo',
            'action'     => 'Acción',
            'status'     => 'Estado',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'groups' => [
            'name'             => 'Nombre',
            'action-category'  => 'Categoría de acción',
            'status'           => 'Estado',
            'delay-count'      => 'Cantidad de retraso',
            'delay-unit'       => 'Unidad de retraso',
            'delay-source'     => 'Origen del retraso',
            'associated-model' => 'Modelo asociado',
            'chaining-type'    => 'Tipo de encadenamiento',
            'decoration-type'  => 'Tipo de decoración',
            'default-user'     => 'Usuario predeterminado',
            'creation-date'    => 'Fecha de creación',
            'last-update'      => 'Última actualización',
        ],

        'filters' => [
            'action'    => 'Acción',
            'status'    => 'Estado',
            'has-delay' => 'Tiene retraso',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de actividad restaurado',
                    'body'  => 'El tipo de actividad se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de actividad eliminado',
                    'body'  => 'El tipo de actividad se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de actividad eliminado permanentemente',
                        'body'  => 'El tipo de actividad se ha eliminado permanentemente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar el tipo de actividad',
                        'body'  => 'El tipo de actividad no se puede eliminar porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de actividad restaurados',
                    'body'  => 'Los tipos de actividad se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de actividad eliminados',
                    'body'  => 'Los tipos de actividad se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipos de actividad eliminados permanentemente',
                    'body'  => 'Los tipos de actividad se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Información general',

                'entries' => [
                    'name'                => 'Tipo de actividad',
                    'name-tooltip'        => 'Introduzca el nombre oficial del tipo de actividad',
                    'action'              => 'Acción',
                    'default-user'        => 'Usuario predeterminado',
                    'plugin'              => 'Complemento',
                    'summary'             => 'Resumen',
                    'note'                => 'Nota',
                ],
            ],

            'delay-information' => [
                'title' => 'Información de retraso',

                'entries' => [
                    'delay-count'            => 'Cantidad de retraso',
                    'delay-unit'             => 'Unidad de retraso',
                    'delay-form'             => 'Origen del retraso',
                    'delay-form-helper-text' => 'Fuente del cálculo del retraso',
                ],
            ],

            'advanced-information' => [
                'title' => 'Información avanzada',

                'entries' => [
                    'icon'                => 'Icono',
                    'decoration-type'     => 'Tipo de decoración',
                    'chaining-type'       => 'Tipo de encadenamiento',
                    'suggest'             => 'Sugerir',
                    'trigger'             => 'Activar',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Estado y configuración',

                'entries' => [
                    'status'               => 'Estado',
                    'keep-done-activities' => 'Mantener actividades completadas',
                ],
            ],
        ],
    ],
];
