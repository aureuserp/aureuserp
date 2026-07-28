<?php

return [
    'title'      => 'Tipo de ausencia',
    'navigation' => [
        'title' => 'Tipo de ausencia',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información general',
                'fields' => [
                    'name'                => 'Título',
                    'approval'            => 'Aprobación',
                    'requires-allocation' => 'Requiere asignación',
                    'employee-requests'   => 'Solicitudes de empleados',
                    'display-option'      => 'Opción de visualización',
                ],
            ],
            'display-option' => [
                'title'  => 'Opción de visualización',
                'fields' => [
                    'color' => 'Color',
                ],
            ],
            'configuration' => [
                'title' => 'Configuración',

                'fields' => [
                    'notified-time-off-officers'          => 'Responsables de ausencias notificados',
                    'take-time-off-in'                    => 'Tomar ausencia en',
                    'public-holiday-included'             => 'Días festivos incluidos',
                    'allow-to-attach-supporting-document' => 'Permitir adjuntar documento justificativo',
                    'show-on-dashboard'                   => 'Mostrar en el panel',
                    'allow-negative-cap'                  => 'Permitir límite negativo',
                    'kind-off-time'                       => 'Tipo de tiempo',
                    'max-negative-cap'                    => 'Límite negativo máximo',
                    'kind-of-time'                        => 'Tipo de ausencia',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Nombre',
            'company-name'              => 'Empresa',
            'color'                     => 'Color',
            'notified-time-officers'    => 'Responsables de tiempo notificados',
            'time-off-approval'         => 'Aprobación de ausencia',
            'requires-allocation'       => 'Requiere asignación',
            'allocation-approval'       => 'Aprobación de asignación',
            'employee-request'          => 'Solicitud de empleado',
        ],

        'filters' => [
            'name'                => 'Nombre',
            'company-name'        => 'Empresa',
            'time-off-approval'   => 'Aprobación de ausencia',
            'requires-allocation' => 'Requiere asignación',
            'time-type'           => 'Tipo de tiempo',
            'request-unit'        => 'Unidad de solicitud',
            'created-by'          => 'Creado por',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo de ausencia eliminado',
                    'body'  => 'El tipo de ausencia se ha eliminado correctamente.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de ausencia restaurado',
                    'body'  => 'El tipo de ausencia se ha restaurado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de ausencia restaurado',
                    'body'  => 'El tipo de ausencia se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de ausencia eliminado',
                    'body'  => 'El tipo de ausencia se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de ausencia eliminado permanentemente',
                        'body'  => 'El tipo de ausencia se ha eliminado permanentemente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar el tipo de ausencia',
                        'body'  => 'El tipo de ausencia no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'name'                => 'Título',
                    'approval'            => 'Aprobación',
                    'requires-allocation' => 'Requiere asignación',
                    'employee-requests'   => 'Solicitudes de empleados',
                    'display-option'      => 'Opción de visualización',
                ],
            ],
            'display-option' => [
                'title'   => 'Opción de visualización',
                'entries' => [
                    'color' => 'Color',
                ],
            ],
            'configuration' => [
                'title' => 'Configuración',

                'entries' => [
                    'notified-time-off-officers'          => 'Responsables de ausencias notificados',
                    'take-time-off-in'                    => 'Tomar ausencia en',
                    'public-holiday-included'             => 'Días festivos incluidos',
                    'allow-to-attach-supporting-document' => 'Permitir adjuntar documento justificativo',
                    'show-on-dashboard'                   => 'Mostrar en el panel',
                    'kind-off-time'                       => 'Tipo de tiempo',
                    'max-negative-cap'                    => 'Límite negativo máximo',
                    'kind-of-time'                        => 'Tipo de ausencia',
                ],
            ],
        ],
    ],
];
