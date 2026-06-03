<?php

return [
    'navigation' => [
        'title' => 'Operaciones',
        'group' => 'Configuración',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'name'              => 'Operación',
                    'name-placeholder'  => 'p. ej. Corte',
                    'bill-of-material'  => 'Lista de materiales',
                    'work-center'       => 'Centro de trabajo',
                    'apply-on-variants' => 'Aplicar en variantes',
                    'company'           => 'Empresa',
                    'blocked-by'        => 'Bloqueado por',
                ],
            ],
            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'time-mode'                  => 'Cálculo de duración',
                    'time-mode-batch'            => 'Basado en',
                    'time-mode-batch-prefix'     => 'las últimas',
                    'time-mode-batch-suffix'     => 'órdenes de trabajo',
                    'manual-cycle-time'          => 'Duración predeterminada',
                    'manual-cycle-time-suffix'   => 'minutos',
                ],
            ],
            'worksheet' => [
                'title'  => 'Hoja de trabajo',
                'fields' => [
                    'worksheet'                => 'Hoja de trabajo',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slide',
                    'google-slide-placeholder' => 'Enlace de Google Slide',
                    'description'              => 'Descripción',
                    'description-placeholder'  => 'Descripción de la operación...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Operación',
            'bill-of-material'  => 'Lista de materiales',
            'work-center'       => 'Centro de trabajo',
            'time-mode'         => 'Cálculo de duración',
            'manual-cycle-time' => 'Duración predeterminada',
            'worksheet-type'    => 'Hoja de trabajo',
            'deleted-at'        => 'Eliminado el',
            'created-at'        => 'Creado el',
            'updated-at'        => 'Actualizado el',
        ],
        'filters' => [
            'work-center'    => 'Centro de trabajo',
            'time-mode'      => 'Cálculo de duración',
            'worksheet-type' => 'Hoja de trabajo',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operación restaurada',
                    'body'  => 'La operación ha sido restaurada correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operación archivada',
                    'body'  => 'La operación ha sido archivada correctamente.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operación eliminada',
                        'body'  => 'La operación ha sido eliminada permanentemente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar la operación',
                        'body'  => 'No es posible eliminar la operación porque está en uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operaciones restauradas',
                    'body'  => 'Las operaciones seleccionadas han sido restauradas correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operaciones archivadas',
                    'body'  => 'Las operaciones seleccionadas han sido archivadas correctamente.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operaciones eliminadas',
                        'body'  => 'Las operaciones seleccionadas han sido eliminadas permanentemente.',
                    ],
                    'error' => [
                        'title' => 'No se pudieron eliminar las operaciones',
                        'body'  => 'Una o más operaciones seleccionadas están en uso.',
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
                    'name'              => 'Operación',
                    'bill-of-material'  => 'Lista de materiales',
                    'work-center'       => 'Centro de trabajo',
                    'apply-on-variants' => 'Aplicar en variantes',
                    'company'           => 'Empresa',
                ],
            ],
            'settings' => [
                'title'   => 'Configuración',
                'entries' => [
                    'time-mode'                => 'Cálculo de duración',
                    'time-mode-batch'          => 'Basado en',
                    'manual-cycle-time'        => 'Duración predeterminada',
                    'manual-cycle-time-suffix' => 'minutos',
                ],
            ],
            'worksheet' => [
                'title'   => 'Hoja de trabajo',
                'entries' => [
                    'worksheet'    => 'Hoja de trabajo',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description'  => 'Descripción',
                ],
            ],
            'record-information' => [
                'title'   => 'Información del registro',
                'entries' => [
                    'created-by'   => 'Creado por',
                    'created-at'   => 'Creado el',
                    'last-updated' => 'Última actualización',
                ],
            ],
        ],
    ],
];
