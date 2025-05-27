<?php

return [
    'title' => 'Tipos de Actividad',

    'navigation' => [
        'title' => 'Tipos de Actividad',
        'group' => 'Empleados',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Información General',

                'fields' => [
                    'name'         => 'Tipo de Actividad',
                    'name-tooltip' => 'Ingrese el nombre oficial del tipo de actividad',
                    'action'       => 'Acción',
                    'default-user' => 'Usuario Predeterminado',
                    'summary'      => 'Resumen',
                    'note'         => 'Nota',
                ],
            ],

            'delay-information' => [
                'title' => 'Información de Retraso',

                'fields' => [
                    'delay-count'             => 'Cantidad de Retraso',
                    'delay-unit'              => 'Unidad de Retraso',
                    'delay-form'              => 'Formulario de Retraso',
                    'delay-form-helper-text' => 'Fuente del cálculo del retraso',
                ],
            ],

            'advanced-information' => [
                'title' => 'Información Avanzada',

                'fields' => [
                    'icon'            => 'Icono',
                    'decoration-type' => 'Tipo de Decoración',
                    'chaining-type'   => 'Tipo de Encadenamiento',
                    'suggest'         => 'Sugerir',
                    'trigger'         => 'Desencadenar',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Estado y Configuración',

                'fields' => [
                    'status'              => 'Estado',
                    'keep-done-activities' => 'Mantener Actividades Hechas',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Tipo de Actividad',
            'summary'    => 'Resumen',
            'planned-in' => 'Planificado En',
            'type'       => 'Tipo',
            'action'     => 'Acción',
            'status'     => 'Estado',
            'created-at' => 'Creado En',
            'updated-at' => 'Actualizado En',
        ],

        'groups' => [
            'name'             => 'Nombre',
            'action-category'  => 'Categoría de Acción',
            'status'           => 'Estado',
            'delay-count'      => 'Cantidad de Retraso',
            'delay-unit'       => 'Unidad de Retraso',
            'delay-source'     => 'Fuente de Retraso',
            'associated-model' => 'Modelo Asociado',
            'chaining-type'    => 'Tipo de Encadenamiento',
            'decoration-type'  => 'Tipo de Decoración',
            'default-user'     => 'Usuario Predeterminado',
            'creation-date'    => 'Fecha de Creación',
            'last-update'      => 'Última Actualización',
        ],

        'filters' => [
            'action'    => 'Acción',
            'status'    => 'Estado',
            'has-delay' => 'Tiene Retraso',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de actividad restaurado',
                    'body'  => 'El tipo de actividad ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de actividad eliminado',
                    'body'  => 'El tipo de actividad ha sido eliminado exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipo de actividad eliminado permanentemente',
                    'body'  => 'El tipo de actividad ha sido eliminado permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de actividad restaurados',
                    'body'  => 'Los tipos de actividad han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de actividad eliminados',
                    'body'  => 'Los tipos de actividad han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipos de actividad eliminados permanentemente',
                    'body'  => 'Los tipos de actividad han sido eliminados permanentemente exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title'   => 'Información General',
                'entries' => [
                    'name'         => 'Tipo de Actividad',
                    'name-tooltip' => 'Ingrese el nombre oficial del tipo de actividad',
                    'action'       => 'Acción',
                    'default-user' => 'Usuario Predeterminado',
                    'plugin'       => 'Plugin',
                    'summary'      => 'Resumen',
                    'note'         => 'Nota',
                ],
            ],

            'delay-information' => [
                'title'   => 'Información de Retraso',
                'entries' => [
                    'delay-count'             => 'Cantidad de Retraso',
                    'delay-unit'              => 'Unidad de Retraso',
                    'delay-form'              => 'Formulario de Retraso',
                    'delay-form-helper-text' => 'Fuente del cálculo del retraso',
                ],
            ],

            'advanced-information' => [
                'title'   => 'Información Avanzada',
                'entries' => [
                    'icon'            => 'Icono',
                    'decoration-type' => 'Tipo de Decoración',
                    'chaining-type'   => 'Tipo de Encadenamiento',
                    'suggest'         => 'Sugerir',
                    'trigger'         => 'Desencadenar',
                ],
            ],

            'status-and-configuration-information' => [
                'title'   => 'Estado y Configuración',
                'entries' => [
                    'status'              => 'Estado',
                    'keep-done-activities' => 'Mantener Actividades Hechas',
                ],
            ],
        ],
    ],
];
