<?php

return [
    'navigation' => [
        'title' => 'Ubicaciones',
        'group' => 'Gestión de Almacenes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'location'                      => 'Ubicación',
                    'location-placeholder'          => 'ej. Stock de Repuestos',
                    'parent-location'               => 'Ubicación Padre',
                    'parent-location-hint-tooltip'  => 'La ubicación principal que abarca esta ubicación. Por ejemplo, la \'Zona de Despacho\' es parte de la ubicación padre \'Puerta 1\'.',
                    'external-notes'                => 'Notas Externas',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'fields' => [
                    'location-type'                     => 'Tipo de Ubicación',
                    'company'                           => 'Empresa',
                    'storage-category'                  => 'Categoría de Almacenamiento',
                    'is-scrap'                          => '¿Es una Ubicación de Desecho?',
                    'is-scrap-hint-tooltip'             => 'Selecciona esta casilla para designar esta ubicación para almacenar productos desechados o dañados.',
                    'is-dock'                           => '¿Es una Ubicación de Muelle?',
                    'is-dock-hint-tooltip'              => 'Selecciona esta casilla para designar esta ubicación para almacenar productos listos para su envío.',
                    'is-replenish'                      => '¿Es una Ubicación de Reposición?',
                    'is-replenish-hint-tooltip'         => 'Activa esta función para recuperar todas las cantidades necesarias para la reposición en esta ubicación.',
                    'logistics'                         => 'Logística',
                    'removal-strategy'                  => 'Estrategia de Remoción',
                    'removal-strategy-hint-tooltip'     => 'Especifica el método predeterminado para determinar el estante, lote y ubicación exactos desde donde recoger los productos. Este método se puede aplicar a nivel de categoría de producto, con una alternativa a las ubicaciones padre si no se establece aquí.',
                    'cyclic-counting'                   => 'Conteo Cíclico',
                    'inventory-frequency'               => 'Frecuencia de Inventario',
                    'last-inventory'                      => 'Último Inventario',
                    'last-inventory-hint-tooltip'     => 'Fecha del último inventario en esta ubicación.',
                    'next-expected'                     => 'Próximo Esperado',
                    'next-expected-hint-tooltip'        => 'Fecha para el próximo inventario planificado según el programa cíclico.',
                ],
            ],

            'additional' => [
                'title'  => 'Información Adicional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'          => 'Ubicación',
            'type'              => 'Tipo',
            'storage-category'  => 'Categoría de Almacenamiento',
            'company'           => 'Empresa',
            'deleted-at'        => 'Eliminado El',
            'created-at'        => 'Creado El',
            'updated-at'        => 'Actualizado El',
        ],

        'groups' => [
            'warehouse'   => 'Almacén',
            'type'          => 'Tipo',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'filters' => [
            'location' => 'Ubicación',
            'type'     => 'Tipo',
            'company'  => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ubicación actualizada',
                    'body'  => 'La ubicación ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ubicación restaurada',
                    'body'  => 'La ubicación ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ubicación eliminada',
                    'body'  => 'La ubicación ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ubicación eliminada permanentemente',
                        'body'  => 'La ubicación ha sido eliminada permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la ubicación',
                        'body'  => 'La ubicación no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir Código de Barras',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ubicaciones restauradas',
                    'body'  => 'Las ubicaciones han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ubicaciones eliminadas',
                    'body'  => 'Las ubicaciones han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ubicaciones eliminadas permanentemente',
                        'body'  => 'Las ubicaciones han sido eliminadas permanentemente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las ubicaciones',
                        'body'  => 'Las ubicaciones no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'entries' => [
                    'location'                      => 'Ubicación',
                    'location-placeholder'          => 'ej. Stock de Repuestos',
                    'parent-location'               => 'Ubicación Padre',
                    'parent-location-hint-tooltip'  => 'La ubicación principal que abarca esta ubicación. Por ejemplo, la \'Zona de Despacho\' es parte de la ubicación padre \'Puerta 1\'.',
                    'external-notes'                => 'Notas Externas',
                ],
            ],

            'settings' => [
                'title'  => 'Configuración',

                'entries' => [
                    'location-type'                     => 'Tipo de Ubicación',
                    'company'                           => 'Empresa',
                    'storage-category'                  => 'Categoría de Almacenamiento',
                    'is-scrap'                          => '¿Es una Ubicación de Desecho?',
                    'is-scrap-hint-tooltip'             => 'Selecciona esta casilla para designar esta ubicación para almacenar productos desechados o dañados.',
                    'is-dock'                           => '¿Es una Ubicación de Muelle?',
                    'is-dock-hint-tooltip'              => 'Selecciona esta casilla para designar esta ubicación para almacenar productos listos para su envío.',
                    'is-replenish'                      => '¿Es una Ubicación de Reposición?',
                    'is-replenish-hint-tooltip'         => 'Activa esta función para recuperar todas las cantidades necesarias para la reposición en esta ubicación.',
                    'logistics'                         => 'Logística',
                    'removal-strategy'                  => 'Estrategia de Remoción',
                    'removal-strategy-hint-tooltip'     => 'Especifica el método predeterminado para determinar el estante, lote y ubicación exactos desde donde recoger los productos. Este método se puede aplicar a nivel de categoría de producto, con una alternativa a las ubicaciones padre si no se establece aquí.',
                    'cyclic-counting'                   => 'Conteo Cíclico',
                    'inventory-frequency'               => 'Frecuencia de Inventario',
                    'last-inventory'                      => 'Último Inventario',
                    'last-inventory-hint-tooltip'     => 'Fecha del último inventario en esta ubicación.',
                    'next-expected'                     => 'Próximo Esperado',
                    'next-expected-hint-tooltip'        => 'Fecha para el próximo inventario planificado según el programa cíclico.',
                ],
            ],

            'additional' => [
                'title'  => 'Información Adicional',
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'created-by'   => 'Creado Por',
                    'created-at'   => 'Creado El',
                    'last-updated' => 'Última Actualización',
                ],
            ],
        ],
    ],
];
