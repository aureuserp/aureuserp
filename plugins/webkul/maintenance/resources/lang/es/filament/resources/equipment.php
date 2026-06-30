<?php

return [
    'navigation' => [
        'group' => 'Mantenimiento',
        'title' => 'Equipo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Información general',
                'fields' => [
                    'name' => 'Nombre',
                    'note' => 'Descripción',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',
                'fields' => [
                    'category'   => 'Categoría de equipo',
                    'team'       => 'Equipo de mantenimiento',
                    'company'    => 'Empresa',
                    'technician' => 'Técnico',
                    'owner'      => 'Propietario',
                    'location'   => 'Utilizado en la ubicación',
                ],
            ],

            'product-information' => [
                'title'  => 'Información del producto',
                'fields' => [
                    'partner'                     => 'Proveedor',
                    'partner-ref'                 => 'Referencia del proveedor',
                    'model'                       => 'Modelo',
                    'serial-no'                   => 'Número de serie',
                    'effective-date'              => 'Fecha de entrada en vigor',
                    'effective-date-hint-tooltip' => 'Se utiliza como punto de partida para calcular el tiempo medio entre fallos.',
                    'cost'                        => 'Costo',
                    'warranty-date'               => 'Fecha de vencimiento de la garantía',
                ],
            ],

            'maintenance' => [
                'title'  => 'Mantenimiento',
                'fields' => [
                    'expected-mtbf' => 'Tiempo medio entre fallos esperado',
                ],
                'suffixes' => [
                    'days' => 'días',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre del equipo',
            'owner'      => 'Propietario',
            'serial-no'  => 'Número de serie',
            'category'   => 'Categoría de equipo',
            'technician' => 'Técnico',
            'company'    => 'Empresa',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'category'   => 'Categoría de equipo',
            'team'       => 'Equipo de mantenimiento',
            'technician' => 'Técnico',
        ],

        'groups' => [
            'category'   => 'Categoría de equipo',
            'owner'      => 'Propietario',
            'technician' => 'Técnico',
            'vendor'     => 'Proveedor',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipo actualizado',
                    'body'  => 'El equipo se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipo restaurado',
                    'body'  => 'El equipo se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo archivado',
                    'body'  => 'El equipo se ha archivado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipo eliminado',
                        'body'  => 'El equipo se ha eliminado de forma permanente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el equipo',
                        'body'  => 'Este equipo está referenciado por otro registro.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipo restaurado',
                    'body'  => 'El equipo seleccionado se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo archivado',
                    'body'  => 'El equipo seleccionado se ha archivado correctamente.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipo creado',
                    'body'  => 'El equipo se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Información general',
                'entries' => [
                    'name' => 'Nombre',
                    'note' => 'Descripción',
                ],
            ],

            'settings' => [
                'title'   => 'Configuración',
                'entries' => [
                    'category'   => 'Categoría de equipo',
                    'team'       => 'Equipo de mantenimiento',
                    'company'    => 'Empresa',
                    'technician' => 'Técnico',
                    'owner'      => 'Propietario',
                    'location'   => 'Utilizado en la ubicación',
                ],
            ],

            'product-information' => [
                'title'   => 'Información del producto',
                'entries' => [
                    'partner'        => 'Proveedor',
                    'partner-ref'    => 'Referencia del proveedor',
                    'model'          => 'Modelo',
                    'serial-no'      => 'Número de serie',
                    'effective-date' => 'Fecha de entrada en vigor',
                    'cost'           => 'Costo',
                    'warranty-date'  => 'Fecha de vencimiento de la garantía',
                ],
            ],

            'maintenance' => [
                'title'   => 'Mantenimiento',
                'entries' => [
                    'expected-mtbf'          => 'Tiempo medio entre fallos esperado',
                    'maintenance-count'      => 'Número de mantenimientos',
                    'maintenance-open-count' => 'Número de mantenimientos abiertos',
                    'assigned-at'            => 'Fecha de asignación',
                    'scraped-at'             => 'Fecha de desecho',
                ],
                'suffixes' => [
                    'days' => 'días',
                ],
            ],
        ],
    ],
];
